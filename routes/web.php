<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PermissoesController;
use Illuminate\Support\Facades\Http;

// Controllers
use App\Http\Controllers\ChampionController;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function(){
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('teste/{gameName}/{tagName}', [ApiController::class, 'getMaestria'])->middleware(['auth', 'verified'])->name('teste');
Route::get('teste/{key}', [ApiController::class, 'getChampionName'])->name('testee');
Route::get('teste/{gameName}/{tagName}', [ApiController::class, 'getHistorico'])->middleware(['auth', 'verified'])->name('testeee');
Route::get('partida/{gameName}/{tagName}', [ApiController::class, 'getUltimaPartida'])->middleware(['auth', 'verified'])->name('partida');


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



require __DIR__.'/settings.php';
