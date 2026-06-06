@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 sm:text-sm';
    $badge = ['lead' => 'bg-amber-100 text-amber-800', 'active' => 'bg-green-100 text-green-800', 'inactive' => 'bg-gray-200 text-gray-700'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('clients.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 dark:text-gray-400">← Voltar para clientes</a>

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

            {{-- Próximo follow-up --}}
            <div class="mt-4 flex flex-wrap items-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
                <div>
                    <label class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Próximo follow-up</label>
                    <input type="datetime-local" wire:model="followup_at" class="{{ $input }}">
                </div>
                <button wire:click="saveFollowup" class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Agendar</button>
                @if ($client->next_followup_at)
                    <button wire:click="clearFollowup" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Limpar</button>
                    <span class="pb-2 text-sm @if ($client->isFollowupOverdue()) font-medium text-red-600 dark:text-red-400 @else text-gray-500 dark:text-gray-400 @endif">
                        @if ($client->isFollowupOverdue()) ⚠ vencido em @else agendado para @endif {{ $client->next_followup_at->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Anexos --}}
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Anexos</h2>

            <div class="flex flex-wrap items-center gap-3">
                <input type="file" wire:model="upload"
                       class="block text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100 dark:text-gray-300">
                <button type="button" wire:click="saveUpload" wire:loading.attr="disabled"
                        class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="upload,saveUpload">Enviar</span>
                    <span wire:loading wire:target="upload,saveUpload">Enviando...</span>
                </button>
                <span class="text-xs text-gray-400">Até 4&nbsp;MB</span>
            </div>
            @error('upload') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            @if ($attachments->isNotEmpty())
                <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($attachments as $attachment)
                        <li class="flex items-center justify-between py-2 text-sm">
                            <span class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                📎 <a href="{{ route('attachments.download', $attachment) }}" class="text-emerald-600 hover:underline dark:text-emerald-400">{{ $attachment->name }}</a>
                                <span class="text-xs text-gray-400">({{ $attachment->humanSize() }})</span>
                            </span>
                            <button wire:click="deleteAttachment({{ $attachment->id }})" wire:confirm="Remover este anexo?"
                                    class="text-xs text-gray-400 hover:text-red-600">Excluir</button>
                        </li>
                    @endforeach
                </ul>
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
                    <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Adicionar</button>
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
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
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
