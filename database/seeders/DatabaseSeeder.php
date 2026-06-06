<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Conta de demonstração (para experimentar sem cadastrar)
        $user = User::updateOrCreate(
            ['email' => 'demo@clientela.app'],
            ['name' => 'Conta Demo', 'password' => bcrypt('password')],
        );

        if ($user->clients()->count() > 0) {
            return;
        }

        $clients = [
            ['name' => 'Marina Costa', 'company' => 'Padaria Pão Quente', 'status' => 'active', 'email' => 'marina@paoquente.com', 'phone' => '(51) 99888-1010'],
            ['name' => 'Eduardo Lima', 'company' => 'Auto Peças Lima', 'status' => 'active', 'email' => 'eduardo@apl.com', 'phone' => '(51) 99777-2020'],
            ['name' => 'Patrícia Gomes', 'company' => 'Studio Pilates', 'status' => 'lead', 'email' => 'patricia@studio.com', 'phone' => '(51) 99666-3030'],
            ['name' => 'Rodrigo Alves', 'company' => 'Mercado Central', 'status' => 'lead', 'email' => 'rodrigo@mercado.com', 'phone' => '(51) 99555-4040'],
            ['name' => 'Carla Mendes', 'company' => 'Salão Beleza Pura', 'status' => 'inactive', 'email' => 'carla@belezapura.com', 'phone' => '(51) 99444-5050'],
            ['name' => 'Fernando Rocha', 'company' => 'Construtora Rocha', 'status' => 'active', 'email' => 'fernando@rocha.com', 'phone' => '(51) 99333-6060'],
        ];

        $interactions = [
            ['type' => 'call', 'notes' => 'Primeiro contato, apresentei a proposta.'],
            ['type' => 'whatsapp', 'notes' => 'Enviei o orçamento pelo WhatsApp.'],
            ['type' => 'meeting', 'notes' => 'Reunião para alinhar escopo do serviço.'],
            ['type' => 'email', 'notes' => 'Follow-up: aguardando retorno sobre o contrato.'],
        ];

        // alguns follow-ups: vencidos e próximos (para o painel)
        $followups = [now()->subDays(3), now()->subDay(), null, now()->addDays(2), null, now()->addDays(5)];

        foreach ($clients as $i => $data) {
            $client = $user->clients()->create([
                ...$data,
                'next_followup_at' => $followups[$i] ?? null,
            ]);

            // 2 a 3 atendimentos por cliente, com datas escalonadas
            for ($j = 0; $j <= ($i % 2) + 1; $j++) {
                $interaction = $interactions[($i + $j) % count($interactions)];
                $client->interactions()->create([
                    ...$interaction,
                    'happened_at' => now()->subDays($j * 3 + $i),
                ]);
            }
        }
    }
}
