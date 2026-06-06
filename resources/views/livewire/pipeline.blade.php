@php
    $cols = ['lead' => 'Leads', 'active' => 'Ativos', 'inactive' => 'Inativos'];
    $dot = ['lead' => 'bg-amber-400', 'active' => 'bg-green-500', 'inactive' => 'bg-gray-400'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Funil de clientes</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Arraste os cartões entre as etapas para mudar o status.</p>
        </div>

        <div wire:ignore
             x-data="{
                init() {
                    this.$el.querySelectorAll('[data-list]').forEach((col) => {
                        window.Sortable.create(col, {
                            group: 'pipeline',
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (e) => {
                                $wire.moveClient(Number(e.item.dataset.id), e.to.dataset.status);
                                this.refresh();
                            },
                        });
                    });
                },
                refresh() {
                    this.$el.querySelectorAll('[data-count]').forEach((b) => {
                        b.textContent = this.$el.querySelector('[data-list=\'' + b.dataset.count + '\']').children.length;
                    });
                },
             }"
             class="grid gap-4 md:grid-cols-3">
            @foreach ($cols as $status => $label)
                <div class="rounded-xl bg-gray-100 p-3 dark:bg-gray-900/50">
                    <div class="mb-3 flex items-center gap-2 px-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <span class="h-2.5 w-2.5 rounded-full {{ $dot[$status] }}"></span>
                        {{ $label }}
                        <span class="ml-auto rounded-full bg-white px-2 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400" data-count="{{ $status }}">
                            {{ ($groups[$status] ?? collect())->count() }}
                        </span>
                    </div>

                    <div data-list="{{ $status }}" class="min-h-[140px] space-y-2">
                        @foreach (($groups[$status] ?? []) as $client)
                            <div data-id="{{ $client->id }}" wire:key="card-{{ $client->id }}"
                                 class="cursor-grab rounded-lg border border-gray-200 bg-white p-3 shadow-sm active:cursor-grabbing dark:border-gray-700 dark:bg-gray-800">
                                <a href="{{ route('clients.show', $client) }}" class="font-medium text-gray-900 hover:text-emerald-600 dark:text-gray-100">{{ $client->name }}</a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $client->company ?: '—' }}</div>
                                <div class="mt-1 flex items-center gap-2 text-xs text-gray-400">
                                    <span>{{ $client->interactions_count }} atendimento(s)</span>
                                    @if ($client->isFollowupOverdue())
                                        <span class="rounded-full bg-red-100 px-1.5 py-0.5 font-medium text-red-700 dark:bg-red-900/40 dark:text-red-300">vencido</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
