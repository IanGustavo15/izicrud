<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dono;
use App\Models\Pet;
use App\Models\Servico;
use App\Models\OrdemServico;
use App\Models\OrdemServicoServico;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
        ]);

        $this->seedClinicaVeterinaria();
    }

    private function seedClinicaVeterinaria()
    {
        $faker = Faker::create('pt_BR');

        // Datas: de 01/01 do ano passado até hoje
        $dataInicio = Carbon::create(date('Y'), 1, 1);
        $dataFim = Carbon::now();
        $diasOperacao = $dataInicio->diffInDays($dataFim);

        // Movimento médio: 3-8 atendimentos/dia (exceto domingos)
        $atendimentosPorDia = rand(3, 8);
        $diasUteis = (int)($diasOperacao * 6 / 7); // Desconta domingos
        $totalAtendimentos = $diasUteis * $atendimentosPorDia;

        // Base de clientes: 60% do total de atendimentos (clientes recorrentes)
        $totalDonos = (int)($totalAtendimentos * 0.6);
        $totalPets = (int)($totalDonos * 1.8); // 1.8 pets por dono em média

        echo "📅 Simulando {$diasOperacao} dias de operação\n";
        echo "👥 Criando {$totalDonos} donos\n";
        echo "🐾 Criando {$totalPets} pets\n";
        echo "📋 Criando {$totalAtendimentos} ordens de serviço\n\n";

        // Criar donos
        for ($i = 1; $i <= $totalDonos; $i++) {
            Dono::create([
                'nome' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'telefone' => $faker->cellphoneNumber,
                'deleted' => 0
            ]);
        }

        // Criar serviços
        $servicosBase = [
            'Consulta Veterinária' => [80, 120],
            'Vacinação Múltipla' => [45, 65],
            'Banho e Tosa Completa' => [25, 50],
            'Exame de Sangue Completo' => [100, 150],
            'Cirurgia Simples' => [200, 400],
            'Castração/Esterilização' => [150, 300],
            'Limpeza Dental' => [80, 120],
            'Ultrassom Abdominal' => [120, 200],
            'Raio-X Digital' => [90, 140],
            'Microchipagem' => [50, 80],
            'Vermifugação' => [15, 30],
            'Aplicação Medicamento' => [20, 40],
            'Curativos e Band.' => [30, 60],
            'Atendimento Emergência' => [200, 500],
            'Internação (diária)' => [100, 200],
            'Tosa Higiênica' => [20, 35],
            'Corte de Unhas' => [15, 25],
            'Fisioterapia' => [60, 100],
            'Acupuntura' => [80, 120],
            'Check-up Geriátrico' => [150, 250],
        ];

        foreach ($servicosBase as $nome => $faixaPreco) {
            Servico::create([
                'nome' => $nome,
                'preco' => $faker->randomFloat(2, $faixaPreco[0], $faixaPreco[1]),
                'ativo' => $faker->boolean(90),
                'deleted' => 0
            ]);
        }

        // Criar pets
        $especies = ['Cachorro', 'Gato', 'Coelho', 'Hamster', 'Passarinho', 'Tartaruga', 'Chinchila'];
        $nomesPets = [
            'Rex', 'Mimi', 'Bolt', 'Luna', 'Thor', 'Nina', 'Max', 'Pipoca',
            'Buddy', 'Bella', 'Charlie', 'Lucy', 'Rocky', 'Lola', 'Duke', 'Molly',
            'Zeus', 'Chloe', 'Bear', 'Sophie', 'Jack', 'Sadie', 'Oliver', 'Maggie',
            'Bob', 'Mel', 'Fred', 'Dora', 'Blue', 'Honey', 'Lucky', 'Zara',
            'Toby', 'Emma', 'Leo', 'Mila', 'Bento', 'Jade', 'Oscar', 'Lara'
        ];

        for ($i = 1; $i <= $totalPets; $i++) {
            $especie = $faker->randomElement($especies);

            $peso = match($especie) {
                'Cachorro' => $faker->randomFloat(1, 1.5, 60),
                'Gato' => $faker->randomFloat(1, 1, 8),
                'Coelho' => $faker->randomFloat(1, 0.5, 4),
                'Hamster' => $faker->randomFloat(2, 0.05, 0.3),
                'Passarinho' => $faker->randomFloat(2, 0.02, 0.5),
                'Tartaruga' => $faker->randomFloat(1, 0.1, 8),
                'Chinchila' => $faker->randomFloat(1, 0.3, 0.8),
            };

            Pet::create([
                'nome' => $faker->randomElement($nomesPets),
                'especie' => $especie,
                'id_dono' => rand(1, $totalDonos),
                'peso' => $peso,
                'deleted' => 0
            ]);
        }

        // Criar ordens de serviço distribuídas ao longo do ano
        $servicosAtivos = Servico::where('ativo', true)->where('deleted', 0)->get();
        $dataAtual = clone $dataInicio;
        $numeroOrdem = 1;

        while ($dataAtual->lte($dataFim)) {
            // Pula domingos
            if ($dataAtual->dayOfWeek === Carbon::SUNDAY) {
                $dataAtual->addDay();
                continue;
            }

            // Variação de movimento: segunda/sexta (3-6), terça-quinta (5-9), sábado (2-4)
            $atendimentosDia = match($dataAtual->dayOfWeek) {
                Carbon::MONDAY, Carbon::FRIDAY => rand(3, 6),
                Carbon::SATURDAY => rand(2, 4),
                default => rand(5, 9)
            };

            for ($i = 0; $i < $atendimentosDia; $i++) {
                $horaAtendimento = $dataAtual->copy()->setTime(rand(8, 17), rand(0, 59));
                $numeroFormatado = $dataAtual->format('Y') . str_pad($numeroOrdem, 5, '0', STR_PAD_LEFT);

                $ordem = OrdemServico::create([
                    'numero' => $numeroFormatado,
                    'id_pet' => rand(1, $totalPets),
                    'valor_total' => 0,
                    'created_at' => $horaAtendimento,
                    'updated_at' => $horaAtendimento,
                    'deleted' => 0
                ]);

                // Distribuição realista de serviços por atendimento
                $rand = rand(1, 100);
                $quantidadeServicos = match(true) {
                    $rand <= 50 => 1,  // 50% - consulta simples
                    $rand <= 80 => 2,  // 30% - consulta + procedimento
                    $rand <= 95 => 3,  // 15% - múltiplos procedimentos
                    default => rand(4, 5) // 5% - casos complexos
                };

                $servicosEscolhidos = $servicosAtivos->random(min($quantidadeServicos, $servicosAtivos->count()));
                $valorTotal = 0;

                foreach ($servicosEscolhidos as $servico) {
                    OrdemServicoServico::create([
                        'id_ordemservico' => $ordem->id,
                        'id_servico' => $servico->id,
                        'deleted' => 0
                    ]);

                    $valorTotal += $servico->preco;
                }

                $ordem->update(['valor_total' => $valorTotal]);
                $numeroOrdem++;
            }

            $dataAtual->addDay();
        }

        echo "✅ Seed concluído! Total de {$numeroOrdem} atendimentos criados.\n";
    }
}
