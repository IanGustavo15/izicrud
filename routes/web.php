<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PermissoesController;

// Controllers
use App\Http\Controllers\SimuladoQuestaoController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\RespostaController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\MetricaController;
use App\Http\Controllers\NotaCorteController;
use App\Http\Controllers\OpcaoController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\QuestaoController;
use App\Http\Controllers\SimuladoController;



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
    Route::controller(SimuladoQuestaoController::class)->group(function () {
        Route::get('/simuladoquestao', 'index')->name('simuladoquestao.index');
        Route::get('/simuladoquestao/create', 'create')->name('simuladoquestao.create');
        Route::post('/simuladoquestao', 'store')->name('simuladoquestao.store');
        Route::get('/simuladoquestao/{simuladoquestao}/edit', 'edit')->name('simuladoquestao.edit');
        Route::put('/simuladoquestao/{simuladoquestao}', 'update')->name('simuladoquestao.update');
        Route::delete('/simuladoquestao/{simuladoquestao}', 'destroy')->name('simuladoquestao.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(ResultadoController::class)->group(function () {
        Route::get('/resultado', 'index')->name('resultado.index');
        Route::get('/resultado/create', 'create')->name('resultado.create');
        Route::post('/resultado', 'store')->name('resultado.store');
        Route::get('/resultado/{resultado}/edit', 'edit')->name('resultado.edit');
        Route::put('/resultado/{resultado}', 'update')->name('resultado.update');
        Route::delete('/resultado/{resultado}', 'destroy')->name('resultado.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(RespostaController::class)->group(function () {
        Route::get('/resposta', 'index')->name('resposta.index');
        Route::get('/resposta/create', 'create')->name('resposta.create');
        Route::post('/resposta', 'store')->name('resposta.store');
        Route::get('/resposta/{resposta}/edit', 'edit')->name('resposta.edit');
        Route::put('/resposta/{resposta}', 'update')->name('resposta.update');
        Route::delete('/resposta/{resposta}', 'destroy')->name('resposta.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(InscricaoController::class)->group(function () {
        Route::get('/inscricao', 'index')->name('inscricao.index');
        Route::get('/inscricao/create', 'create')->name('inscricao.create');
        Route::post('/inscricao', 'store')->name('inscricao.store');
        Route::get('/inscricao/{inscricao}/edit', 'edit')->name('inscricao.edit');
        Route::put('/inscricao/{inscricao}', 'update')->name('inscricao.update');
        Route::delete('/inscricao/{inscricao}', 'destroy')->name('inscricao.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(MetricaController::class)->group(function () {
        Route::get('/metrica', 'index')->name('metrica.index');
        Route::get('/metrica/create', 'create')->name('metrica.create');
        Route::post('/metrica', 'store')->name('metrica.store');
        Route::get('/metrica/{metrica}/edit', 'edit')->name('metrica.edit');
        Route::put('/metrica/{metrica}', 'update')->name('metrica.update');
        Route::delete('/metrica/{metrica}', 'destroy')->name('metrica.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(NotaCorteController::class)->group(function () {
        Route::get('/notacorte', 'index')->name('notacorte.index');
        Route::get('/notacorte/create', 'create')->name('notacorte.create');
        Route::post('/notacorte', 'store')->name('notacorte.store');
        Route::get('/notacorte/{notacorte}/edit', 'edit')->name('notacorte.edit');
        Route::put('/notacorte/{notacorte}', 'update')->name('notacorte.update');
        Route::delete('/notacorte/{notacorte}', 'destroy')->name('notacorte.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(OpcaoController::class)->group(function () {
        Route::get('/opcao', 'index')->name('opcao.index');
        Route::get('/opcao/create', 'create')->name('opcao.create');
        Route::post('/opcao', 'store')->name('opcao.store');
        Route::get('/opcao/{opcao}/edit', 'edit')->name('opcao.edit');
        Route::put('/opcao/{opcao}', 'update')->name('opcao.update');
        Route::delete('/opcao/{opcao}', 'destroy')->name('opcao.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(RankController::class)->group(function () {
        Route::get('/rank', 'index')->name('rank.index');
        Route::get('/rank/create', 'create')->name('rank.create');
        Route::post('/rank', 'store')->name('rank.store');
        Route::get('/rank/{rank}/edit', 'edit')->name('rank.edit');
        Route::put('/rank/{rank}', 'update')->name('rank.update');
        Route::delete('/rank/{rank}', 'destroy')->name('rank.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(QuestaoController::class)->group(function () {
        Route::get('/questao', 'index')->name('questao.index');
        Route::get('/questao/create', 'create')->name('questao.create');
        Route::post('/questao', 'store')->name('questao.store');
        Route::get('/questao/{questao}/edit', 'edit')->name('questao.edit');
        Route::put('/questao/{questao}', 'update')->name('questao.update');
        Route::delete('/questao/{questao}', 'destroy')->name('questao.destroy');
    });
});
Route::group([
    'middleware' => ['auth', 'verified', 'permissao:99'],
], function () {
    Route::controller(SimuladoController::class)->group(function () {
        Route::get('/simulado', 'index')->name('simulado.index');
        Route::get('/simulado/create', 'create')->name('simulado.create');
        Route::post('/simulado', 'store')->name('simulado.store');
        Route::get('/simulado/{simulado}/edit', 'edit')->name('simulado.edit');
        Route::put('/simulado/{simulado}', 'update')->name('simulado.update');
        Route::delete('/simulado/{simulado}', 'destroy')->name('simulado.destroy');
    });
});


require __DIR__.'/settings.php';
