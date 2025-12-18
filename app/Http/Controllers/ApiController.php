<?php

namespace App\Http\Controllers;

use App\Models\Champion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class ApiController extends Controller
{
    public function getPuuid($gameName, $tagLine){
        // $gameName = 'sTOPsister';
        // $tagLine = 'sob';
        $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}";
        $retornoAPI = Http::withHeaders([
        'X-Riot-Token' => env('API_KEY')
        ])->get($url)->json();
        // dd($retornoAPI['puuid']);
        return $retornoAPI;
    }
    public function getMaestria($gameName, $tagLine){
        $puuid = $this->_getPuuid($gameName, $tagLine);
        $url = "https://br1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-puuid/{$puuid}/top?count=5";
        $retornoAPI = Http::withHeaders([
        'X-Riot-Token' => env('API_KEY')
        ])->get($url)->json();
        foreach ($retornoAPI as $key => $top) {
            $championNome = $this->getChampionName($top['championId']);
            $retornoAPI[$key]['championNome'] = $championNome;
        }
        // dd($retornoAPI);
        return $retornoAPI;
    }

    public function _getPuuid($gameName, $tagLine){
        $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}";
        $retornoAPI = Http::withHeaders([
        'X-Riot-Token' => env('API_KEY')
        ])->get($url)->json();
        return $retornoAPI['puuid'];
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
    public function getChampionName($key){
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
}
