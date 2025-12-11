<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PermissoesController;

// Controllers
use App\Http\Controllers\TrabalhadorController;
use App\Http\Controllers\ServicoOrdemDeServicoController;
use App\Http\Controllers\OrdemDeServicoController;
use App\Http\Controllers\PecaServicoController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\PecaController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/sempermissao', function () {
    return Inertia::render('SemPermissao');
})->middleware(['auth', 'verified'])->name('sem-permissao');

Route::group([
    'middleware' => ['auth', 'verified', 'permissao:0'],
], function () {
    Route::controller(PermissoesController::class)->group(function () {
        Route::get('/permissoes', 'index')->name('permissoes.index');
        Route::get('/permissoes/create', 'create')->name('permissoes.create');
        Route::post('/permissoes', 'store')->name('permissoes.store');
        Route::get('/permissoes/{permissoes}/edit', 'edit')->name('permissoes.edit');
        Route::put('/permissoes/{permissoes}', 'update')->name('permissoes.update');
        Route::delete('/permissoes/{permissoes}', 'destroy')->name('permissoes.destroy');
        Route::get('/permissoes/atribuir', 'atribuirPermissoes')->name('permissoes.atribuir');
        Route::post('/permissoes/atribuir', 'atribuirStore')->name('permissoes.atribuir.store');
        Route::get('/permissoes/usuarios', 'listUsuarios')->name('permissoes.usuarios');
    });
});


// Rotas
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(TrabalhadorController::class)->group(function () {
        Route::get('/trabalhador', 'index')->name('trabalhador.index');
        Route::get('/trabalhador/create', 'create')->name('trabalhador.create');
        Route::post('/trabalhador', 'store')->name('trabalhador.store');
        Route::get('/trabalhador/{trabalhador}/edit', 'edit')->name('trabalhador.edit');
        Route::put('/trabalhador/{trabalhador}', 'update')->name('trabalhador.update');
        Route::delete('/trabalhador/{trabalhador}', 'destroy')->name('trabalhador.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:0'],
], function () {
    Route::controller(ServicoOrdemDeServicoController::class)->group(function () {
        Route::get('/servicoordemdeservico', 'index')->name('servicoordemdeservico.index');
        Route::get('/servicoordemdeservico/create', 'create')->name('servicoordemdeservico.create');
        Route::post('/servicoordemdeservico', 'store')->name('servicoordemdeservico.store');
        Route::get('/servicoordemdeservico/{servicoordemdeservico}/edit', 'edit')->name('servicoordemdeservico.edit');
        Route::put('/servicoordemdeservico/{servicoordemdeservico}', 'update')->name('servicoordemdeservico.update');
        Route::delete('/servicoordemdeservico/{servicoordemdeservico}', 'destroy')->name('servicoordemdeservico.destroy');

    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:3'],
], function () {
    Route::controller(OrdemDeServicoController::class)->group(function () {
        Route::get('/ordemdeservico', 'index')->name('ordemdeservico.index');
        Route::get('/ordemdeservico/create', 'create')->name('ordemdeservico.create');
        Route::post('/ordemdeservico', 'store')->name('ordemdeservico.store');
        Route::get('/ordemdeservico/{ordemdeservico}/edit', 'edit')->name('ordemdeservico.edit');
        Route::put('/ordemdeservico/{ordemdeservico}', 'update')->name('ordemdeservico.update');
        Route::delete('/ordemdeservico/{ordemdeservico}', 'destroy')->name('ordemdeservico.destroy');

        Route::get('/ordemdeservico/getVeiculoPorCliente/{id_cliente}', 'getVeiculoPorCliente')->name('ordemdeservico.getVeiculoPorCliente');

        Route::put('/ordemdeservico/finalizarOrdem/{id}', 'finalizarOrdem')->name('ordemdeservico.finalizarOrdem');
        Route::put('/ordemdeservico/cancelarOrdem/{id}', 'cancelarOrdem')->name('ordemdeservico.cancelarOrdem');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:0'],
], function () {
    Route::controller(PecaServicoController::class)->group(function () {
        Route::get('/pecaservico', 'index')->name('pecaservico.index');
        Route::get('/pecaservico/create', 'create')->name('pecaservico.create');
        Route::post('/pecaservico', 'store')->name('pecaservico.store');
        Route::get('/pecaservico/{pecaservico}/edit', 'edit')->name('pecaservico.edit');
        Route::put('/pecaservico/{pecaservico}', 'update')->name('pecaservico.update');
        Route::delete('/pecaservico/{pecaservico}', 'destroy')->name('pecaservico.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:2'],
], function () {
    Route::controller(ServicoController::class)->group(function () {
        Route::get('/servico', 'index')->name('servico.index');
        Route::get('/servico/create', 'create')->name('servico.create');
        Route::post('/servico', 'store')->name('servico.store');
        Route::get('/servico/{servico}/edit', 'edit')->name('servico.edit');
        Route::put('/servico/{servico}', 'update')->name('servico.update');
        Route::delete('/servico/{servico}', 'destroy')->name('servico.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:2'],
], function () {
    Route::controller(PecaController::class)->group(function () {
        Route::get('/peca', 'index')->name('peca.index');
        Route::get('/peca/create', 'create')->name('peca.create');
        Route::post('/peca', 'store')->name('peca.store');
        Route::get('/peca/{peca}/edit', 'edit')->name('peca.edit');
        Route::put('/peca/{peca}', 'update')->name('peca.update');
        Route::delete('/peca/{peca}', 'destroy')->name('peca.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:3'],
], function () {
    Route::controller(VeiculoController::class)->group(function () {
        Route::get('/veiculo', 'index')->name('veiculo.index');
        Route::get('/veiculo/create', 'create')->name('veiculo.create');
        Route::post('/veiculo', 'store')->name('veiculo.store');
        Route::get('/veiculo/{veiculo}/edit', 'edit')->name('veiculo.edit');
        Route::put('/veiculo/{veiculo}', 'update')->name('veiculo.update');
        Route::delete('/veiculo/{veiculo}', 'destroy')->name('veiculo.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:3'],
], function () {
    Route::controller(ClienteController::class)->group(function () {
        Route::get('/cliente', 'index')->name('cliente.index');
        Route::get('/cliente/create', 'create')->name('cliente.create');
        Route::post('/cliente', 'store')->name('cliente.store');
        Route::get('/cliente/{cliente}/edit', 'edit')->name('cliente.edit');
        Route::put('/cliente/{cliente}', 'update')->name('cliente.update');
        Route::delete('/cliente/{cliente}', 'destroy')->name('cliente.destroy');
    });
});


require __DIR__.'/settings.php';
