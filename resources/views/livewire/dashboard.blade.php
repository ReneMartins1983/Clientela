@php
    $typeLabels = \App\Models\Interaction::TYPE_LABELS;
@endphp

<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Painel</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Visão geral da sua carteira de clientes</p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @php
                $cards = [
                    ['Total de clientes', $total, 'text-gray-900 dark:text-gray-100'],
                    ['Leads', $byStatus['lead'] ?? 0, 'text-amber-600'],
                    ['Ativos', $byStatus['active'] ?? 0, 'text-green-600'],
                    ['Inativos', $byStatus['inactive'] ?? 0, 'text-gray-500'],
                ];
            @endphp
            @foreach ($cards as [$label, $value, $color])
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</p>
                    <p class="mt-1 text-3xl font-bold {{ $color }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Follow-ups vencidos --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">
                    ⚠ Follow-ups vencidos
                    <span class="rounded-full bg-red-100 px-2 text-xs text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ $overdue->count() }}</span>
                </h2>
                @forelse ($overdue as $client)
                    <a href="{{ route('clients.show', $client) }}" class="flex items-center justify-between border-b border-gray-100 py-2 text-sm last:border-0 hover:text-emerald-600 dark:border-gray-700">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $client->name }}</span>
                        <span class="text-red-600 dark:text-red-400">{{ $client->next_followup_at->format('d/m/Y') }}</span>
                    </a>
                @empty
                    <p class="py-4 text-sm text-gray-400">Nenhum follow-up vencido. 👍</p>
                @endforelse
            </div>

            {{-- Próximos follow-ups --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Próximos follow-ups</h2>
                @forelse ($upcoming as $client)
                    <a href="{{ route('clients.show', $client) }}" class="flex items-center justify-between border-b border-gray-100 py-2 text-sm last:border-0 hover:text-emerald-600 dark:border-gray-700">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $client->name }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $client->next_followup_at->format('d/m/Y') }}</span>
                    </a>
                @empty
                    <p class="py-4 text-sm text-gray-400">Nenhum follow-up agendado.</p>
                @endforelse
            </div>
        </div>

        {{-- Atendimentos recentes --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Atendimentos recentes</h2>
            @forelse ($recent as $interaction)
                <div class="flex items-center justify-between border-b border-gray-100 py-2 text-sm last:border-0 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">{{ $typeLabels[$interaction->type] }}</span>
                        <a href="{{ route('clients.show', $interaction->client) }}" class="font-medium text-gray-800 hover:text-emerald-600 dark:text-gray-200">{{ $interaction->client->name }}</a>
                        <span class="hidden truncate text-gray-500 sm:inline dark:text-gray-400">— {{ \Illuminate\Support\Str::limit($interaction->notes, 50) }}</span>
                    </div>
                    <span class="text-gray-400">{{ $interaction->happened_at->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="py-4 text-sm text-gray-400">Nenhum atendimento registrado ainda.</p>
            @endforelse
        </div>
    </div>
</div>
