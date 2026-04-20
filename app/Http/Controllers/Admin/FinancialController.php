<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Client\Models\Client;
use App\Domain\Financial\Models\Invoice;
use App\Domain\Financial\Models\Payment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::orderBy('company_name')->get(['id', 'uuid', 'company_name', 'trade_name']);

        // ── Pagamentos ──────────────────────────────────────────
        $payments = Payment::with('client')
            ->when($request->search, fn($q, $v) =>
                $q->whereHas('client', fn($q) =>
                    $q->where('company_name', 'like', "%{$v}%")
                      ->orWhere('trade_name',  'like', "%{$v}%")
                )
            )
            ->when($request->status,    fn($q, $v) => $q->where('status', $v))
            ->when($request->client_id, fn($q, $uuid) =>
                $q->whereHas('client', fn($q) => $q->where('uuid', $uuid))
            )
            ->when($request->month, fn($q, $v) =>
                $q->where('due_date', 'like', \Carbon\Carbon::parse($v . '-01')->format('Y-m') . '%')
            )
            ->orderBy('due_date')
            ->paginate(20)
            ->withQueryString();

        // ── Notas fiscais ────────────────────────────────────────
        $invoices = Invoice::with('client')
            ->when($request->inv_client_id, fn($q, $uuid) =>
                $q->whereHas('client', fn($q) => $q->where('uuid', $uuid))
            )
            ->when($request->inv_month, fn($q, $v) =>
                $q->where('reference_month', \Carbon\Carbon::parse($v . '-01')->toDateString())
            )
            ->orderByDesc('reference_month')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', compact('clients', 'payments', 'invoices'))
            ->with('financial_tab', $request->tab ?? session('financial_tab', 'payments'));
    }

    public function store(StorePaymentRequest $request)
    {
        $client = Client::where('uuid', $request->client_id)->firstOrFail();

        Payment::create([
            'uuid'           => Str::uuid(),
            'client_id'      => $client->id,
            'amount'         => $request->amount,
            'due_date'       => $request->due_date,
            'status'         => $request->status ?? 'pending',
            'payment_method' => $request->payment_method,
            'reference'      => $request->reference,
            'notes'          => $request->notes,
        ]);

        return back()->with('success', 'Pagamento criado com sucesso.')->with('financial_tab', 'payments');
    }

    public function markPaid(Payment $payment)
    {
        $payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Pagamento marcado como pago.')->with('financial_tab', 'payments');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Pagamento removido.')->with('financial_tab', 'payments');
    }

    // ── Notas Fiscais ──────────────────────────────────────────

    public function storeInvoice(Request $request)
    {
        $request->validate([
            'client_id'       => ['required', 'string', 'exists:clients,uuid'],
            'invoice_number'  => ['required', 'string', 'max:50'],
            'reference_month' => ['required', 'date_format:Y-m'],
            'amount'          => ['required', 'numeric', 'min:0'],
            'issue_date'      => ['required', 'date'],
            'pdf_file'        => ['required', 'file', 'mimes:pdf', 'max:15360'], // 15 MB
        ]);

        $client = Client::where('uuid', $request->client_id)->firstOrFail();

        // Armazena em storage/app/private/invoices/{client_uuid}/
        $path = $request->file('pdf_file')->storeAs(
            "invoices/{$client->uuid}",
            Str::uuid() . '.pdf',
            'local' // fora de public/
        );

        Invoice::create([
            'uuid'            => Str::uuid(),
            'client_id'       => $client->id,
            'invoice_number'  => $request->invoice_number,
            'amount'          => $request->amount,
            'issue_date'      => $request->issue_date,
            'reference_month' => \Carbon\Carbon::parse($request->reference_month . '-01')->toDateString(),
            'file_path'       => $path,
            'file_disk'       => 'local',
        ]);

        return back()->with('success', 'Nota fiscal enviada com sucesso.')->with('financial_tab', 'invoices');
    }

    public function downloadInvoice(Invoice $invoice): StreamedResponse
    {
        abort_if(! $invoice->file_path, 404);
        abort_unless(Storage::disk($invoice->file_disk)->exists($invoice->file_path), 404);

        return Storage::disk($invoice->file_disk)->download(
            $invoice->file_path,
            "NF-{$invoice->invoice_number}.pdf"
        );
    }

    public function destroyInvoice(Invoice $invoice)
    {
        if ($invoice->file_path) {
            Storage::disk($invoice->file_disk)->delete($invoice->file_path);
        }

        $invoice->delete();

        return back()->with('success', 'Nota fiscal removida.')->with('financial_tab', 'invoices');
    }
}

