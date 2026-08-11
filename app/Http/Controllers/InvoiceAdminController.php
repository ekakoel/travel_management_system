<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoices\UpdateInvoiceBankRequest;
use App\Http\Requests\StoreAdditionalInvoiceRequest;
use App\Http\Requests\UpdateAdditionalInvoiceRequest;
use App\Models\AdditionalInvoice;
use App\Models\InvoiceAdmin;
use App\Services\Invoices\InvoiceDetailService;
use App\Services\Invoices\InvoiceIndexService;
use App\Services\Invoices\InvoiceMutationService;

class InvoiceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(InvoiceIndexService $invoiceIndex)
    {
        return view('backend.finance.invoices.index', $invoiceIndex->data());
    }

    public function view_detail_invoice(InvoiceAdmin $invoice, InvoiceDetailService $invoiceDetail)
    {
        return view('backend.finance.invoices.detail', $invoiceDetail->data($invoice));
    }

    public function func_add_additional_inv(
        StoreAdditionalInvoiceRequest $request,
        InvoiceAdmin $invoice,
        InvoiceMutationService $mutations
    ) {
        $this->ensureAdjustmentsAreEditable($invoice);
        $mutations->createAdjustment($invoice, $request->validated());

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', __('invoices.adjustment_added'));
    }

    public function func_update_additional_inv(
        UpdateAdditionalInvoiceRequest $request,
        AdditionalInvoice $additionalInvoice,
        InvoiceMutationService $mutations
    ) {
        $invoice = $additionalInvoice->invoice()->with('reservations')->firstOrFail();
        $this->ensureAdjustmentsAreEditable($invoice);
        $mutations->updateAdjustment($additionalInvoice, $request->validated());

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', __('invoices.adjustment_updated'));
    }

    public function destroy_additional_inv(
        AdditionalInvoice $additionalInvoice,
        InvoiceMutationService $mutations
    ) {
        $invoice = $additionalInvoice->invoice()->with('reservations')->firstOrFail();
        $this->ensureAdjustmentsAreEditable($invoice);
        $mutations->deleteAdjustment($additionalInvoice);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', __('invoices.adjustment_removed'));
    }

    public function updateBank(
        UpdateInvoiceBankRequest $request,
        InvoiceAdmin $invoice,
        InvoiceMutationService $mutations
    ) {
        $invoice->loadMissing('reservations');
        abort_if($invoice->reservations?->status === 'Completed' || (float) $invoice->balance <= 0, 403);
        $mutations->updateBank($invoice, (int) $request->validated('bank_id'));

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', __('invoices.bank_updated'));
    }

    private function ensureAdjustmentsAreEditable(InvoiceAdmin $invoice): void
    {
        $invoice->loadMissing('reservations');
        abort_if(in_array($invoice->reservations?->status, ['Active', 'Completed'], true), 403);
    }
}
