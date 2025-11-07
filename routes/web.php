<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PermissoesController;

// Controllers
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\ClienteController;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    'middleware' => ['auth', 'verified', 'permissao:99'],
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
