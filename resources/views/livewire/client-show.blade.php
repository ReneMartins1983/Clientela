@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 sm:text-sm';
    $badge = ['lead' => 'bg-amber-100 text-amber-800', 'active' => 'bg-green-100 text-green-800', 'inactive' => 'bg-gray-200 text-gray-700'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('clients.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Voltar para clientes</a>

        {{-- Cabeçalho do cliente --}}
        <div class="mt-3 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $client->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400">{{ $client->company ?: 'Sem empresa' }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $badge[$client->status] }}">
                    {{ \App\Models\Client::STATUS_LABELS[$client->status] }}
                </span>
            </div>
            <div class="mt-4 grid gap-2 text-sm text-gray-600 dark:text-gray-300 sm:grid-cols-2">
                @if ($client->email)<div>📧 {{ $client->email }}</div>@endif
                @if ($client->phone)<div>📞 {{ $client->phone }}</div>@endif
            </div>
            @if ($client->notes)
                <p class="mt-4 whitespace-pre-wrap rounded-lg bg-gray-50 p-3 text-sm text-gray-600 dark:bg-gray-900/50 dark:text-gray-300">{{ $client->notes }}</p>
            @endif
        </div>

        {{-- Novo atendimento --}}
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Registrar atendimento</h2>
            <form wire:submit="addInteraction" class="space-y-3">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                        <select wire:model="type" class="{{ $input }}">
                            @foreach (\App\Models\Interaction::TYPE_LABELS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quando</label>
                        <input type="datetime-local" wire:model="happened_at" class="{{ $input }}">
                        @error('happened_at') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Anotações *</label>
                    <textarea wire:model="notes" rows="2" class="{{ $input }}" placeholder="O que foi tratado..."></textarea>
                    @error('notes') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Adicionar</button>
                </div>
            </form>
        </div>

        {{-- Histórico --}}
        <div class="mt-6">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Histórico de atendimentos ({{ $interactions->count() }})
            </h2>
            <div class="space-y-3">
                @forelse ($interactions as $interaction)
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    {{ \App\Models\Interaction::TYPE_LABELS[$interaction->type] }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $interaction->happened_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="mt-1 whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $interaction->notes }}</p>
                        </div>
                        <button wire:click="deleteInteraction({{ $interaction->id }})" wire:confirm="Remover este atendimento?"
                                class="text-xs text-gray-400 hover:text-red-600">Excluir</button>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        Nenhum atendimento registrado ainda.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
