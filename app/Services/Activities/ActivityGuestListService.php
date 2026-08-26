<?php

namespace App\Services\Activities;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ActivityGuestListService
{
    public const MANUAL_THRESHOLD = 10;
    public const MAX_UPLOAD_KILOBYTES = 2048;

    private const REQUIRED_HEADERS = [
        'guest name',
        'age category',
        'sex',
        'phone number',
    ];

    private const AGE_VALUES = [
        'adult' => 'Adult',
        'child' => 'Child',
    ];

    private const SEX_VALUES = [
        'male' => 'Male',
        'female' => 'Female',
    ];

    public function parseUpload(UploadedFile $file, int $expectedGuests): array
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $guests = match ($extension) {
            'csv' => $this->parseCsv($file),
            'xlsx' => $this->parseXlsx($file),
            default => throw ValidationException::withMessages([
                'guest_list' => __('activities.detail.order.guest_list_invalid_format'),
            ]),
        };

        if (count($guests) !== $expectedGuests) {
            throw ValidationException::withMessages([
                'guest_list' => __('activities.detail.order.guest_list_count_mismatch', [
                    'actual' => count($guests),
                    'expected' => $expectedGuests,
                ]),
            ]);
        }

        return $guests;
    }

    public function normalizeManualGuests(array $guests, int $guestCount): array
    {
        $normalized = collect($guests)
            ->map(fn ($guest, $index) => $this->normalizeGuestRow([
                $guest['name'] ?? '',
                $guest['age'] ?? '',
                $guest['sex'] ?? '',
                $guest['phone'] ?? '',
            ], ((int) $index) + 1))
            ->filter(fn ($guest) => $guest['name'] !== '' || $guest['age'] !== '' || $guest['sex'] !== '' || $guest['phone'] !== '')
            ->values();

        if ($normalized->isEmpty() || $normalized->count() > $guestCount) {
            throw ValidationException::withMessages([
                'guests' => __('activities.detail.order.guest_count_mismatch'),
            ]);
        }

        return $this->validateParsedRows($normalized->all(), 'guests');
    }

    public function csvTemplateContent(): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Guest Name', 'Age Category', 'Sex', 'Phone Number']);
        fputcsv($handle, ['John Smith', 'Adult', 'Male', '+62812345678']);
        fputcsv($handle, ['Jane Smith', 'Child', 'Female', '']);
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return (string) $content;
    }

    public function xlsxTemplateContent(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'activity-guest-template-');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml([
            ['Guest Name', 'Age Category', 'Sex', 'Phone Number'],
            ['John Smith', 'Adult', 'Male', '+62812345678'],
            ['Jane Smith', 'Child', 'Female', ''],
        ]));
        $zip->close();

        $content = file_get_contents($tmp);
        @unlink($tmp);

        return (string) $content;
    }

    private function parseCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            throw ValidationException::withMessages([
                'guest_list' => __('activities.detail.order.guest_list_empty'),
            ]);
        }

        $header = fgetcsv($handle);
        $this->validateHeader($header ?: []);
        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->isBlankRow($row)) {
                continue;
            }

            $rows[] = $this->normalizeGuestRow($row, count($rows) + 2);
        }

        fclose($handle);

        return $this->validateParsedRows($rows, 'guest_list');
    }

    private function parseXlsx(UploadedFile $file): array
    {
        $zip = new ZipArchive();

        if ($zip->open($file->getRealPath()) !== true) {
            throw ValidationException::withMessages([
                'guest_list' => __('activities.detail.order.guest_list_invalid_format'),
            ]);
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml') ?: null;
        $zip->close();

        if (! $sheetXml) {
            throw ValidationException::withMessages([
                'guest_list' => __('activities.detail.order.guest_list_empty'),
            ]);
        }

        $sharedStrings = $this->parseSharedStrings($sharedStringsXml);
        $rows = $this->parseSheetRows($sheetXml, $sharedStrings);
        $header = array_shift($rows) ?: [];

        $this->validateHeader($header);

        return $this->validateParsedRows(
            collect($rows)
                ->reject(fn ($row) => $this->isBlankRow($row))
                ->map(fn ($row, $index) => $this->normalizeGuestRow($row, $index + 2))
                ->values()
                ->all(),
            'guest_list'
        );
    }

    private function validateHeader(array $header): void
    {
        $normalized = collect($header)
            ->map(fn ($value) => Str::of((string) $value)->lower()->squish()->toString())
            ->values()
            ->all();

        if ($normalized !== self::REQUIRED_HEADERS) {
            throw ValidationException::withMessages([
                'guest_list' => __('activities.detail.order.guest_list_header_invalid'),
            ]);
        }
    }

    private function validateParsedRows(array $rows, string $errorKey): array
    {
        if (empty($rows)) {
            throw ValidationException::withMessages([
                $errorKey => __('activities.detail.order.guest_list_empty'),
            ]);
        }

        foreach ($rows as $row) {
            $rowNumber = (int) ($row['row_number'] ?? 0);

            if ($row['name'] === '') {
                throw ValidationException::withMessages([
                    $errorKey => __('activities.detail.order.guest_name_required', ['row' => $rowNumber]),
                ]);
            }

            if (! in_array($row['age'], self::AGE_VALUES, true)) {
                throw ValidationException::withMessages([
                    $errorKey => __('activities.detail.order.guest_age_category_invalid', ['row' => $rowNumber]),
                ]);
            }

            if (! in_array($row['sex'], self::SEX_VALUES, true)) {
                throw ValidationException::withMessages([
                    $errorKey => __('activities.detail.order.guest_sex_invalid', ['row' => $rowNumber]),
                ]);
            }
        }

        return collect($rows)
            ->map(fn ($row) => collect($row)->except('row_number')->all())
            ->values()
            ->all();
    }

    private function normalizeGuestRow(array $row, int $rowNumber): array
    {
        return [
            'name' => trim((string) ($row[0] ?? '')),
            'age' => $this->normalizeAgeCategory($row[1] ?? ''),
            'sex' => $this->normalizeSex($row[2] ?? ''),
            'phone' => trim((string) ($row[3] ?? '')),
            'date_of_birth' => '',
            'identification_type' => '',
            'identification_no' => '',
            'is_leader' => false,
            'row_number' => $rowNumber,
        ];
    }

    private function normalizeAgeCategory(mixed $value): string
    {
        $key = Str::of((string) $value)->lower()->squish()->toString();

        return self::AGE_VALUES[$key] ?? trim((string) $value);
    }

    private function normalizeSex(mixed $value): string
    {
        $key = Str::of((string) $value)->lower()->squish()->toString();

        return self::SEX_VALUES[$key] ?? trim((string) $value);
    }

    private function isBlankRow(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
    }

    private function parseSharedStrings(?string $xml): array
    {
        if (! $xml) {
            return [];
        }

        $document = simplexml_load_string($xml);

        if (! $document) {
            return [];
        }

        $strings = [];
        foreach ($document->si as $item) {
            $strings[] = trim((string) ($item->t ?? ''));
        }

        return $strings;
    }

    private function parseSheetRows(string $xml, array $sharedStrings): array
    {
        $document = simplexml_load_string($xml);

        if (! $document || ! isset($document->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($document->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $columnIndex = $this->columnIndex($ref);
                $value = (string) ($cell->v ?? '');

                if ((string) $cell['t'] === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ((string) $cell['t'] === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                }

                $values[$columnIndex] = trim($value);
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = array_replace(array_fill(0, max(array_keys($values)) + 1, ''), $values);
            }
        }

        return $rows;
    }

    private function columnIndex(string $cellRef): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($cellRef));
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max($index - 1, 0);
    }

    private function sheetXml(array $rows): string
    {
        $rowXml = collect($rows)
            ->map(function (array $row, int $rowIndex) {
                $cells = collect($row)
                    ->map(function ($value, int $columnIndex) use ($rowIndex) {
                        $cell = $this->columnName($columnIndex).($rowIndex + 1);

                        return '<c r="'.$cell.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $value, ENT_XML1).'</t></is></c>';
                    })
                    ->implode('');

                return '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
            })
            ->implode('');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$rowXml.'</sheetData></worksheet>';
    }

    private function columnName(int $index): string
    {
        $name = '';
        $index += 1;

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Guest List" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>';
    }
}
