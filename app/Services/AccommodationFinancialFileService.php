<?php

namespace App\Services;

use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Support\Pdf\InvoiceLocale;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccommodationFinancialFileService
{
    public const DISK = 'private';
    public const PAYMENT_ROOT = 'accommodation/payments';
    public const INVOICE_ROOT = 'accommodation/invoices';
    public const TRANSPORT_PAYMENT_ROOT = 'transport/payments';
    public const TRANSPORT_INVOICE_ROOT = 'transport/invoices';
    public const TOUR_PAYMENT_ROOT = 'tour/payments';
    public const TOUR_INVOICE_ROOT = 'tour/invoices';
    public const ACTIVITY_PAYMENT_ROOT = 'activity/payments';
    public const ACTIVITY_INVOICE_ROOT = 'activity/invoices';

    private const LEGACY_RECEIPT_ROOT = 'storage/receipt';
    private const LEGACY_INVOICE_ROOT = 'storage/document';

    public function isAccommodationOrder(Orders $order): bool
    {
        return in_array($order->service, Orders::ACCOMMODATION_SERVICES, true);
    }

    public function isProtectedPublicOrder(Orders $order): bool
    {
        return $this->isAccommodationOrder($order)
            || $order->service === Orders::PUBLIC_TRANSPORT_SERVICE
            || $order->service === Orders::PUBLIC_TOUR_SERVICE
            || $order->service === Orders::PUBLIC_ACTIVITY_SERVICE;
    }

    public function privateReceiptPath(Orders $order, string $filename): string
    {
        return $this->paymentRootForOrder($order).'/'.$order->id.'/'.$filename;
    }

    public function privateInvoicePath(Orders $order, InvoiceAdmin $invoice, string $locale): string
    {
        return $this->invoiceRootForOrder($order).'/'.$order->id.'/'.$this->invoiceFilename($order, $invoice, $locale);
    }

    public function invoiceFilename(Orders $order, InvoiceAdmin $invoice, string $locale): string
    {
        $invoiceNumber = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $invoice->inv_no) ?: 'invoice';
        $locale = InvoiceLocale::assertSupported($locale);

        return "invoice-{$invoiceNumber}-{$order->id}_{$locale}.pdf";
    }

    public function resolveReceiptFile(Orders $order, PaymentConfirmation $payment): ?array
    {
        $storedPath = trim((string) $payment->receipt_img);

        if ($storedPath === '' || $this->isUnsafeRelativePath($storedPath)) {
            return null;
        }

        if (Str::startsWith($storedPath, self::PAYMENT_ROOT.'/')) {
            return $this->resolvePrivateFile(
                $storedPath,
                self::PAYMENT_ROOT.'/'.$order->id,
                $this->receiptDownloadName($payment),
                true
            );
        }

        if (Str::startsWith($storedPath, self::TRANSPORT_PAYMENT_ROOT.'/')) {
            return $this->resolvePrivateFile(
                $storedPath,
                self::TRANSPORT_PAYMENT_ROOT.'/'.$order->id,
                $this->receiptDownloadName($payment),
                true
            );
        }

        if (Str::startsWith($storedPath, self::TOUR_PAYMENT_ROOT.'/')) {
            return $this->resolvePrivateFile(
                $storedPath,
                self::TOUR_PAYMENT_ROOT.'/'.$order->id,
                $this->receiptDownloadName($payment),
                true
            );
        }

        if (Str::startsWith($storedPath, self::ACTIVITY_PAYMENT_ROOT.'/')) {
            return $this->resolvePrivateFile(
                $storedPath,
                self::ACTIVITY_PAYMENT_ROOT.'/'.$order->id,
                $this->receiptDownloadName($payment),
                true
            );
        }

        return $this->resolveLegacyFile(
            $this->legacyReceiptCandidate($storedPath),
            $this->legacyReceiptRoot(),
            $this->receiptDownloadName($payment),
            true
        );
    }

    public function resolveInvoiceFile(Orders $order, InvoiceAdmin $invoice, string $locale): ?array
    {
        $locale = InvoiceLocale::assertSupported($locale);
        $privatePath = $this->privateInvoicePath($order, $invoice, $locale);

        return $this->resolvePrivateFile(
            $privatePath,
            $this->invoiceRootForOrder($order).'/'.$order->id,
            "invoice-{$invoice->inv_no}-{$locale}.pdf",
            false
        ) ?: $this->resolveLegacyFile(
            $this->legacyInvoiceCandidate($order, $invoice, $locale),
            $this->legacyInvoiceRoot(),
            "invoice-{$invoice->inv_no}-{$locale}.pdf",
            false
        );
    }

    public function fileResponseHeaders(array $file, string $disposition): array
    {
        return [
            'Content-Type' => $file['mime'],
            'Content-Disposition' => $disposition.'; filename="'.$file['download_name'].'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ];
    }

    private function resolvePrivateFile(string $path, string $allowedRoot, string $downloadName, bool $receipt): ?array
    {
        if (
            $this->isUnsafeRelativePath($path)
            || !Str::startsWith($path, rtrim($allowedRoot, '/').'/')
            || !Storage::disk(self::DISK)->exists($path)
        ) {
            return null;
        }

        $absolutePath = Storage::disk(self::DISK)->path($path);

        return $this->fileData($absolutePath, $downloadName, $receipt);
    }

    private function resolveLegacyFile(?string $absolutePath, string $allowedRoot, string $downloadName, bool $receipt): ?array
    {
        if (!$absolutePath) {
            return null;
        }

        $realPath = realpath($absolutePath);
        $realRoot = realpath($allowedRoot);

        if (!$realPath || !$realRoot || !Str::startsWith($realPath, rtrim($realRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $this->fileData($realPath, $downloadName, $receipt);
    }

    private function fileData(string $absolutePath, string $downloadName, bool $receipt): ?array
    {
        $mime = File::mimeType($absolutePath) ?: 'application/octet-stream';
        $allowed = $receipt
            ? ['image/jpeg', 'image/png', 'application/pdf']
            : ['application/pdf'];

        if (!in_array($mime, $allowed, true)) {
            return null;
        }

        return [
            'absolute_path' => $absolutePath,
            'mime' => $mime,
            'download_name' => $this->safeDownloadName($downloadName),
        ];
    }

    private function legacyReceiptCandidate(string $storedPath): ?string
    {
        $path = Str::of($storedPath)
            ->replace('\\', '/')
            ->replaceStart('/storage/receipt/', '')
            ->replaceStart('storage/receipt/', '')
            ->replaceStart('receipt/', '')
            ->toString();

        if ($path === '' || $this->isUnsafeRelativePath($path) || Str::contains($path, '/')) {
            return null;
        }

        $publicDiskPath = Storage::disk('public')->path('receipt/'.$path);

        return File::exists($publicDiskPath)
            ? $publicDiskPath
            : public_path(self::LEGACY_RECEIPT_ROOT.'/'.$path);
    }

    private function legacyInvoiceCandidate(Orders $order, InvoiceAdmin $invoice, string $locale): ?string
    {
        $filename = "invoice-{$invoice->inv_no}-{$order->id}_{$locale}.pdf";

        if ($this->isUnsafeRelativePath($filename) || Str::contains(str_replace('\\', '/', $filename), '/')) {
            return null;
        }

        $publicDiskPath = Storage::disk('public')->path('document/'.$filename);

        return File::exists($publicDiskPath)
            ? $publicDiskPath
            : public_path(self::LEGACY_INVOICE_ROOT.'/'.$filename);
    }

    private function legacyReceiptRoot(): string
    {
        $publicDiskRoot = Storage::disk('public')->path('receipt');

        return File::isDirectory($publicDiskRoot)
            ? $publicDiskRoot
            : public_path(self::LEGACY_RECEIPT_ROOT);
    }

    private function legacyInvoiceRoot(): string
    {
        $publicDiskRoot = Storage::disk('public')->path('document');

        return File::isDirectory($publicDiskRoot)
            ? $publicDiskRoot
            : public_path(self::LEGACY_INVOICE_ROOT);
    }

    private function paymentRootForOrder(Orders $order): string
    {
        return match ($order->service) {
            Orders::PUBLIC_TRANSPORT_SERVICE => self::TRANSPORT_PAYMENT_ROOT,
            Orders::PUBLIC_TOUR_SERVICE => self::TOUR_PAYMENT_ROOT,
            Orders::PUBLIC_ACTIVITY_SERVICE => self::ACTIVITY_PAYMENT_ROOT,
            default => self::PAYMENT_ROOT,
        };
    }

    private function invoiceRootForOrder(Orders $order): string
    {
        return match ($order->service) {
            Orders::PUBLIC_TRANSPORT_SERVICE => self::TRANSPORT_INVOICE_ROOT,
            Orders::PUBLIC_TOUR_SERVICE => self::TOUR_INVOICE_ROOT,
            Orders::PUBLIC_ACTIVITY_SERVICE => self::ACTIVITY_INVOICE_ROOT,
            default => self::INVOICE_ROOT,
        };
    }

    private function receiptDownloadName(PaymentConfirmation $payment): string
    {
        $extension = pathinfo((string) $payment->receipt_img, PATHINFO_EXTENSION) ?: 'bin';

        return 'payment-receipt-'.$payment->id.'.'.$extension;
    }

    private function safeDownloadName(string $filename): string
    {
        return preg_replace('/[^A-Za-z0-9._-]/', '-', $filename) ?: 'financial-file';
    }

    private function isUnsafeRelativePath(string $path): bool
    {
        $normalized = str_replace('\\', '/', $path);

        return $normalized === ''
            || Str::startsWith($normalized, ['/'])
            || preg_match('/^[A-Za-z]:/', $normalized) === 1
            || preg_match('~(^|/)\.\.($|/)~', $normalized) === 1;
    }
}
