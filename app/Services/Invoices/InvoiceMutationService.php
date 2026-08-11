<?php

namespace App\Services\Invoices;

use App\Models\AdditionalInvoice;
use App\Models\InvoiceAdmin;
use Illuminate\Support\Facades\DB;

class InvoiceMutationService
{
    public function createAdjustment(InvoiceAdmin $invoice, array $data): AdditionalInvoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            InvoiceAdmin::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            return $invoice->additionalinv()->create($this->adjustmentPayload($data));
        }, 3);
    }

    public function updateAdjustment(AdditionalInvoice $adjustment, array $data): AdditionalInvoice
    {
        return DB::transaction(function () use ($adjustment, $data) {
            $locked = AdditionalInvoice::query()->whereKey($adjustment->id)->lockForUpdate()->firstOrFail();
            $locked->update($this->adjustmentPayload($data));

            return $locked->refresh();
        }, 3);
    }

    public function deleteAdjustment(AdditionalInvoice $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            AdditionalInvoice::query()->whereKey($adjustment->id)->lockForUpdate()->firstOrFail()->delete();
        }, 3);
    }

    public function updateBank(InvoiceAdmin $invoice, int $bankId): void
    {
        DB::transaction(function () use ($invoice, $bankId) {
            InvoiceAdmin::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail()->update(['bank_id' => $bankId]);
        }, 3);
    }

    private function adjustmentPayload(array $data): array
    {
        $rate = round((float) $data['rate'], 2);
        $unit = round((float) $data['unit'], 2);
        $times = round((float) $data['times'], 2);

        return [
            'date' => $data['date'],
            'description' => trim($data['description']),
            'rate' => $rate,
            'unit' => $unit,
            'times' => $times,
            'amount' => round($rate * $unit * $times, 2),
        ];
    }
}
