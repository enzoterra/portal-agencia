<?php

namespace App\Http\Controllers\Client;

use App\Domain\Financial\Models\Invoice;
use App\Domain\Financial\Models\Payment;
use App\Domain\Report\Models\Report;
use App\Http\Controllers\Controller;
use App\Domain\Setting\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $clientId = auth()->user()->client_id;
        $year = $request->input('year', now()->year);

        // Próximos pagamentos pendentes/vencidos
        $pendingPayments = Payment::whereIn('status', ['pending', 'overdue', 'under_review'])
            ->orderBy('due_date')
            ->get();

        // Pagamento mais próximo (para o card de resumo)
        $nextPayment = $pendingPayments->first();

        // Histórico do ano selecionado
        $history = Payment::whereYear('due_date', $year)
            ->orderByDesc('due_date')
            ->get();

        // Anos disponíveis para o filtro
        $years = Payment::selectRaw('EXTRACT(YEAR FROM due_date) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year');

        // Notas fiscais (todas, para a seção de NFs)
        $invoices = Invoice::orderByDesc('reference_month')->get();

        // Índice NF por mês ('Y-m' => Invoice) — para a coluna NF no histórico
        $invoiceByMonth = $invoices->keyBy(
            fn($inv) => \Carbon\Carbon::parse($inv->reference_month)->format('Y-m')
        );

        // ROI por mês — só carrega se o cliente tem show_roi ativo
        $roiByMonth = collect();
        $lastMonthRoi = null;

        if (auth()->user()->client->show_roi) {
            $reports = Report::where('status', 'published')
                ->whereYear('reference_month', $year)
                ->get(['reference_month', 'roi']);

            $roiByMonth = $reports->keyBy(
                fn($r) => \Carbon\Carbon::parse($r->reference_month)->format('Y-m')
            )->map(fn($r) => $r->roi);

            // ROI do mês anterior para o card de resumo
            $lastMonthKey = now()->subMonth()->format('Y-m');
            $lastMonthRoi = $roiByMonth->get($lastMonthKey);
        }

        // Pagamentos pagos no ano (para card alternativo quando show_roi = false)
        $paidThisYear = Payment::whereYear('due_date', now()->year)
            ->where('status', 'paid')
            ->count();

        // Chave PIX das configurações
        $settings = app(SettingService::class);
        $pixKey = $settings->get('pix_key', '');
        $pixName = $settings->get('pix_name', '');
        $pixCity = $settings->get('pix_city', '');

        return view('client.payments.index', compact(
            'pendingPayments',
            'nextPayment',
            'history',
            'years',
            'invoices',
            'invoiceByMonth',
            'roiByMonth',
            'lastMonthRoi',
            'paidThisYear',
            'pixKey',
            'pixName',
            'pixCity'
        ));
    }

    public function downloadInvoice(Invoice $invoice): StreamedResponse
    {
        // BelongsToClient garante que a NF pertence ao cliente logado
        abort_if(!$invoice->file_path, 404);
        abort_unless(Storage::disk($invoice->file_disk)->exists($invoice->file_path), 404);

        return Storage::disk($invoice->file_disk)->download(
            $invoice->file_path,
            "NF-{$invoice->invoice_number}.pdf"
        );
    }

    /**
     * Gera o QR code PIX como imagem PNG (sem salvar em disco).
     * Requer: composer require chillerlan/php-qrcode
     */
    public function pixQrCode(Request $request)
    {
        $settings = app(SettingService::class);
        $pixKey = $settings->get('pix_key', '');
        $pixName = $settings->get('pix_name', '');
        $pixCity = $settings->get('pix_city', '');

        abort_if(empty($pixKey) || empty($pixName) || empty($pixCity), 404, 'Dados do PIX incompletos.');

        $amount = $request->input('amount') ? (float) $request->input('amount') : null;
        $txid = $request->input('txid', 'PGTO');

        $payload = app(\App\Domain\Financial\Services\PixService::class)->generatePayload($pixKey, $pixName, $pixCity, $amount, $txid);

        $options = new \chillerlan\QRCode\QROptions([
            'outputType' => 'svg',
            'imageBase64' => false,
            'scale' => 8,
            'imageTransparent' => false,
        ]);

        $svg = (new \chillerlan\QRCode\QRCode($options))->render($payload);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function notifyPayment(Payment $payment)
    {
        // Só permite notificar se estiver pendente ou vencido
        if (!$payment->isPending() && !$payment->isOverdue()) {
            return back()->with('error', 'Este pagamento não pode ser notificado.');
        }

        $payment->update([
            'status' => 'under_review',
        ]);

        return back()->with('success', 'Pagamento notificado! O administrador irá validar em breve.');
    }
}
