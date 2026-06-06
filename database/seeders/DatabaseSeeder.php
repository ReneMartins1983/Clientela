<?php

namespace Database\Seeders;

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

        $notes = [
            'call' => ['Primeiro contato, apresentei a empresa.', 'Liguei para retomar a negociação.', 'Cliente pediu para retornar a ligação.'],
            'email' => ['Enviei o orçamento por e-mail.', 'Follow-up: aguardando retorno da proposta.', 'Mandei o contrato para assinatura.'],
            'meeting' => ['Reunião de alinhamento de escopo.', 'Apresentação da proposta presencial.', 'Reunião de fechamento.'],
            'whatsapp' => ['Tirei dúvidas pelo WhatsApp.', 'Enviei catálogo pelo WhatsApp.', 'Cliente confirmou interesse por mensagem.'],
            'note' => ['Indicação de outro cliente.', 'Prefere atendimento pela manhã.', 'Orçamento aprovado internamente.'],
        ];
        $types = array_keys($notes);

        // [nome, empresa, status, follow-up (dias; neg=vencido, null=sem), nº atendimentos]
        $fleet = [
            ['Beatriz Carvalho', 'Floricultura Bela Flor', 'lead', -5, 0],
            ['Gustavo Henrique', 'Oficina do Gustavo', 'lead', -2, 1],
            ['Larissa Moreira', 'Ateliê Larissa', 'lead', -1, 1],
            ['Tiago Fontes', 'Fontes Contabilidade', 'lead', null, 0],
            ['Sandra Aparecida', 'Doces da Sandra', 'lead', 2, 2],
            ['Marcelo Pires', 'Pires Transportes', 'lead', 6, 3],
            ['Renata Bittencourt', 'Clínica Sorrir', 'active', 4, 3],
            ['Cláudia Nunes', 'Mercearia da Cláudia', 'active', 9, 5],
            ['Felipe Andrade', 'Andrade Materiais', 'active', null, 4],
            ['Vanessa Lopes', 'Pet Shop AuAu', 'active', 14, 6],
            ['Ricardo Teixeira', 'Tech Teixeira', 'active', 3, 4],
            ['Juliana Prado', 'Prado Eventos', 'active', null, 5],
            ['Paulo Vidal', 'Vidal Calçados', 'inactive', null, 2],
            ['Mônica Freitas', 'Salão da Mônica', 'inactive', null, 1],
        ];

        foreach ($fleet as $i => [$name, $company, $status, $followup, $count]) {
            $client = $user->clients()->create([
                'name' => $name,
                'company' => $company,
                'status' => $status,
                'email' => str_replace(' ', '.', mb_strtolower($name)).'@exemplo.com',
                'phone' => '(51) 9'.(7000 + $i * 137).'-'.(1000 + $i * 211),
                'next_followup_at' => $followup === null ? null : now()->addDays($followup),
            ]);

            for ($j = 0; $j < $count; $j++) {
                $type = $types[($i + $j) % count($types)];
                $client->interactions()->create([
                    'type' => $type,
                    'notes' => $notes[$type][($i + $j) % 3],
                    'happened_at' => now()->subDays($j * 4 + ($i % 5) + 1),
                ]);
            }
        }
    }
}
