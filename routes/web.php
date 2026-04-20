<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Client;
use App\Http\Controllers\GoogleOAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
| Apenas visitantes não autenticados acessam login.
| Logout disponível para qualquer usuário autenticado.
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin']);
    Route::post('/entrar', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/sair', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/privacidade', fn() => view('privacy'))->name('privacy');

/*
|--------------------------------------------------------------------------
| Portal do Cliente
|--------------------------------------------------------------------------
| Todas as rotas exigem:
|   - Usuário autenticado
|   - Role "client"
|   - EnsureClientOwnership (GlobalScope + verificação explícita)
|
| Rotas estáticas (ex: financeiro/notas) registradas ANTES das dinâmicas
| (ex: financeiro/pix/{uuid}) para evitar captura incorreta pelo roteador.
*/
Route::middleware(['auth', 'role:client', 'client.ownership'])
    ->prefix('portal')
    ->name('cliente.')
    ->group(function () {

        // Dashboard
        Route::get('painel', [Client\DashboardController::class, 'index'])->name('painel');

        // Relatórios
        Route::get('relatorios', [Client\ReportController::class, 'index'])->name('relatorios.index');
        Route::get('relatorios/{report:uuid}', [Client\ReportController::class, 'show'])->name('relatorios.show');

        // Financeiro
        Route::get('financeiro', [Client\PaymentController::class, 'index'])->name('financeiro.index');
        Route::post('financeiro/pagamento/{payment:uuid}/notificar', [Client\PaymentController::class, 'notifyPayment'])->name('financeiro.notificar');
        Route::get('financeiro/notas/{invoice:uuid}/baixar', [Client\PaymentController::class, 'downloadInvoice'])->name('financeiro.nota.baixar');
        Route::get('financeiro/pix/qrcode', [Client\PaymentController::class, 'pixQrCode'])->name('financeiro.pix.qrcode');

        // Mídias
        Route::get('midias', [Client\MediaController::class, 'index'])->name('midias.index');

        // Calendário
        Route::get('calendario', [Client\CalendarController::class, 'index'])->name('calendario.index');
    });

/*
|--------------------------------------------------------------------------
| Painel Administrativo
|--------------------------------------------------------------------------
| Todas as rotas exigem autenticação + role admin ou super_admin.
|
| Ordem de registro dentro de cada recurso:
|   1. Rotas com segmentos estáticos (ex: /financeiro/notas)
|   2. Rotas com parâmetros dinâmicos  (ex: /financeiro/{payment:uuid})
| Isso evita que o roteador capture segmentos fixos como parâmetros UUID.
*/
Route::middleware(['auth', 'role:admin|super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────────
        Route::get('painel', [Admin\DashboardController::class, 'index'])->name('painel');

        // ── Clientes ─────────────────────────────────────────────────
        Route::resource('clientes', Admin\ClientController::class)
            ->parameters(['clientes' => 'client:uuid']);

        // ── Relatórios ───────────────────────────────────────────────
        Route::post('relatorios/{report:uuid}/publicar', [Admin\ReportController::class, 'publish'])->name('relatorios.publicar');
        Route::post('relatorios/{report:uuid}/arquivar', [Admin\ReportController::class, 'archive'])->name('relatorios.arquivar');

        Route::resource('relatorios', Admin\ReportController::class)
            ->parameters(['relatorios' => 'report:uuid']);

        // ── Financeiro ───────────────────────────────────────────────
        Route::post('financeiro/notas', [Admin\FinancialController::class, 'storeInvoice'])->name('financeiro.notas.guardar');
        Route::get('financeiro/notas/{invoice:uuid}/baixar', [Admin\FinancialController::class, 'downloadInvoice'])->name('financeiro.notas.baixar');
        Route::delete('financeiro/notas/{invoice:uuid}', [Admin\FinancialController::class, 'destroyInvoice'])->name('financeiro.notas.excluir');

        Route::get('financeiro', [Admin\FinancialController::class, 'index'])->name('financeiro.index');
        Route::post('financeiro', [Admin\FinancialController::class, 'store'])->name('financeiro.guardar');
        Route::patch('financeiro/{payment:uuid}/pago', [Admin\FinancialController::class, 'markPaid'])->name('financeiro.marcar-pago');
        Route::delete('financeiro/{payment:uuid}', [Admin\FinancialController::class, 'destroy'])->name('financeiro.excluir');

        // ── Mídias ───────────────────────────────────────────────────
        Route::resource('midias', Admin\MediaController::class)
            ->parameters(['midias' => 'media:uuid']);

        // ── Permissões e Usuários ────────────────────────────────────
        Route::get('permissoes', [Admin\PermissionController::class, 'index'])->name('permissoes.index');
        Route::post('permissoes', [Admin\PermissionController::class, 'store'])->name('permissoes.guardar');
        Route::put('permissoes/{user}', [Admin\PermissionController::class, 'update'])->name('permissoes.atualizar');
        Route::patch('permissoes/{user}/alternar', [Admin\PermissionController::class, 'toggleActive'])->name('permissoes.alternar');
        Route::delete('permissoes/{user}', [Admin\PermissionController::class, 'destroy'])->name('permissoes.excluir');

        // ── Configurações ─────────────────────────────────────────────
        Route::post('configuracoes/manutencao', [Admin\SettingController::class, 'maintenance'])->name('configuracoes.manutencao');
        Route::get('configuracoes', [Admin\SettingController::class, 'index'])->name('configuracoes.index');
        Route::put('configuracoes', [Admin\SettingController::class, 'update'])->name('configuracoes.atualizar');

        Route::prefix('google')->name('google.')->group(function () {
            Route::get('callback', [GoogleOAuthController::class, 'callback'])->name('callback');
            Route::get('redirecionar', [GoogleOAuthController::class, 'redirect'])->name('redirecionar');
            Route::delete('desconectar', [GoogleOAuthController::class, 'disconnect'])->name('desconectar');
        });
    });