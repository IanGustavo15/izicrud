<?php

namespace App\Http\Controllers;

use App\Models\Champion;
use App\Models\Mastery;
use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public function getPuuid($gameName, $tagLine)
    {
        // $gameName = 'sTOPsister';
        // $tagLine = 'sob';
        $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}";
        $retornoAPI = Http::withHeaders([
            'X-Riot-Token' => env('API_KEY'),
        ])
            ->get($url)
            ->json();
            Player::updateOrCreate([
                'game_name' => $gameName,
                'tag_line' => $tagLine,
                'puuid' => $retornoAPI['puuid'],
                ]);
        // dd($retornoAPI['puuid']);
        return $retornoAPI;
    }
    public function getMaestria($gameName, $tagLine)
    {
        $puuid = $this->_getPuuid($gameName, $tagLine);
        $url = "https://br1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-puuid/{$puuid}/top?count=5";
        $retornoAPI = Http::withHeaders([
            'X-Riot-Token' => env('API_KEY'),
        ])
            ->get($url)
            ->json();
        foreach ($retornoAPI as $key => $top) {
            $championNome = $this->getChampionNameByDB($top['championId']);
            $retornoAPI[$key]['championNome'] = $championNome;
            // dd($retornoAPI[$key]);
            // dd($retornoAPI[$key]['championPoints'], $retornoAPI[$key]['championNome']);
            Mastery::updateOrCreate(
                [
                    'player' => $puuid,
                    'points' => $retornoAPI[$key]['championPoints'],
                    'champion' => $retornoAPI[$key]['championNome']
                ]);
        }
        // dd($retornoAPI);
        return $retornoAPI;
    }

    public function _getPuuid($gameName, $tagLine)
    {
        return Cache::remember("puuid_{$gameName}_{$tagLine}", 60 * 60 * 24 * 30, function () use ($gameName, $tagLine) {
            $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}";
            $retornoAPI = Http::withHeaders([
                'X-Riot-Token' => env('API_KEY'),
            ])
                ->get($url)
                ->json();
            Player::updateOrCreate([
                'game_name' => $gameName,
                'tag_line' => $tagLine,
                'puuid' => $retornoAPI['puuid'
            ],
                ]);
                // dd($retornoAPI);
            return $retornoAPI['puuid'];
        });
    }

    public function getHistorico($gameName, $tagLine)
    {
        $puuid = $this->_getPuuid($gameName, $tagLine);
        return Cache::remember("historico_{$puuid}", 600, function () use ($puuid) {
            $url = "https://americas.api.riotgames.com/lol/match/v5/matches/by-puuid/{$puuid}/ids?start=0&count=10&";
            $retornoAPI = Http::withHeaders([
                'X-Riot-Token' => env('API_KEY'),
            ])
                ->get($url)
                ->json();
            return $retornoAPI;
        });
    }

    public function getPartida($matchId)
    {
        return Cache::remember("partida_{$matchId}", 60 * 60 * 24 * 30, function () use ($matchId) {
            $url = "https://americas.api.riotgames.com/lol/match/v5/matches/{$matchId}";
            $retornoAPI = Http::withHeaders([
                'X-Riot-Token' => env('API_KEY'),
            ])
                ->get($url)
                ->json();
            return $retornoAPI;
        });
    }

    public function getUltimaPartida($gameName, $tagLine)
    {
        $partidas = $this->getHistorico($gameName, $tagLine);

        $firstMatchId = $partidas[0];
        $detalhesPartida = $this->getPartida($firstMatchId);

        return $detalhesPartida;
    }
    public function getUltimoKDA($gameName, $tagLine)
    {
        $puuid = $this->_getPuuid($gameName, $tagLine);
        $partidas = $this->getHistorico($gameName, $tagLine);
        $firstMatchId = $partidas[0];
        $detalhesPartida = $this->getPartida($firstMatchId);
        $participants = $detalhesPartida['info']['participants'];
        // dd($detalhesPartida);
        // dd($participants);
        $pessoasPorPuuid = array_column($participants, null, 'puuid');
        $selecionado = $pessoasPorPuuid["$puuid"];
        // dd($pessoasPorPuuid);
        // dd($selecionado);
        return [
            'Campeão' => $selecionado['championName'],
            'KDA' => $selecionado['challenges']['kda'],
            'Abates' => $selecionado['kills'],
            'Mortes' => $selecionado['deaths'],
            'Assistências' => $selecionado['assists'],
            'Dano a campeões' => $selecionado['totalDamageDealtToChampions'],
        ];
    }

    public function getDezPartidas($gameName, $tagLine)
    {
        $partidas = $this->getHistorico($gameName, $tagLine);
        $dezPartidas = [];
        for ($i = 0; $i < 10; $i++) {
            if (isset($partidas[$i])) {
                $matchId = $partidas[$i];
                $dezPartidas[] = $this->getPartida($matchId);
            }
        }
        return $dezPartidas;
    }

    public function getTodosKDA($gameName, $tagLine)
    {
        $puuid = $this->_getPuuid($gameName, $tagLine);
        $detalhesPartidas = $this->getDezPartidas($gameName, $tagLine);
        // dd($detalhesPartidas);
        $listaKDA = [];
        foreach ($detalhesPartidas as $det) {
            // dd($det);
            $participants = $det['info']['participants'];
            $pessoasPorPuuid = array_column($participants, null, 'puuid');
            $selecionado = $pessoasPorPuuid["$puuid"];
            $listaKDA[] = [
                'Campeão' => $selecionado['championName'],
                'KDA' => $selecionado['challenges']['kda'],
                'Abates' => $selecionado['kills'],
                'Mortes' => $selecionado['deaths'],
                'Assistências' => $selecionado['assists'],
                'Dano a campeões' => $selecionado['totalDamageDealtToChampions'],
            ];
        }
        return $listaKDA;
        // dd($participants);
        // dd($selecionado);
        // dd($participants);
    }

    public function verificarBanco($model, $id){
        $query = $model::query();
        $query->where('id', $id);
        $ultimaAtualizacao = $query->max('updated_at');
        if (!$ultimaAtualizacao) {
            return false;
        }
        $agora = now();
        $diferenca = $agora->diffInMinutes($ultimaAtualizacao);
        return $diferenca <= 10;
    }

    public function testeVerificarDados($model = null, $id = null)
    {
        $modelMap = [
            'player' => Player::class,
            'mastery' => Mastery::class,
            'champion' => Champion::class,
        ];

        if (!$model) {
            $resultados = [];
            foreach ($modelMap as $nome => $classe) {
                $resultados[$nome.'_todos'] = $this->verificarBanco($classe, null);
                $resultados[$nome.'_id_1'] = $this->verificarBanco($classe, 1);
            }

            return response()->json([
                'message' => 'testando todos os modelos',
                'resultados' => $resultados,
                'timestamp' => now()
            ]);
        }

        $modelClass = $modelMap[strtolower($model)] ?? null;

        if (!$modelClass) {
            return response()->json([
                'error' => 'Modelo não encontrado',
                'modelos_disponveis' => array_keys($modelMap),
                'modelo_solicitado' => $model
            ], 404);
        }

        $resultado = $this->verificarBanco($modelClass, $id);

        return response()->json([
            'message' => "testando modelo {$model}",
            'modelo' => $model,
            'record_id' => $id,
            'resultado' => $resultado,
            'explicacao' => $resultado ? 'Ta bom demais' : 'Ta ruim essa porra',
            'timestamp' => now()
        ]);
    }
    public function championsList(){
        $url = "https://ddragon.leagueoflegends.com/cdn/15.24.1/data/en_US/champion.json";
        $retornoAPI = Http::get($url)->json()['data'];

        // dd($retornoAPI);

        foreach ($retornoAPI as $champ) {
            Champion::updateOrCreate(
    ['key' => $champ['key']],
        [
                    'api_id' => $champ['id'],
                    'name'   => $champ['name'],
                    'title'  => $champ['title']
                ]
            );
        }
    }

    public function getChampionNameByJSON($key)
    {
        $caminho = storage_path('app/data/champions.json');
        if (file_exists($caminho)) {
            $conteudoJson = file_get_contents($caminho);
            $data = json_decode($conteudoJson, true);

            $champions = $data['data'];
            $championsByKey = array_column($champions, null, 'key');
            if (isset($championsByKey[$key])) {
                return $championsByKey[$key]['name'];
            }
            return null;
        }
    }

    public function getChampionNameByDB($key){
        $champions = Champion::where('key', $key)->value('name') ?? 'Campeão não encontrado';
        // Outras Opções de chamar um valor específico
        // $champions = Champion::where('key', $key)->first()?->name;
        // $champions = Champion::where('key', $key)->firstOrFail()->name;
        // $champions = Champion::where('key', $key)->first()->name ?? 'campeão não encontrado';
        return $champions;
    }
}
