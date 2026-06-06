@php
    $input = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 sm:text-sm';
    $badge = ['lead' => 'bg-amber-100 text-amber-800', 'active' => 'bg-green-100 text-green-800', 'inactive' => 'bg-gray-200 text-gray-700'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Cabeçalho --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Clientes</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie seus clientes e atendimentos</p>
            </div>
            <button wire:click="create"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                + Novo cliente
            </button>
        </div>

        {{-- Filtros --}}
        <div class="mb-4 flex flex-col gap-3 sm:flex-row">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, empresa ou e-mail..."
                   class="{{ $input }} sm:max-w-xs">
            <select wire:model.live="statusFilter" class="{{ $input }} sm:max-w-[12rem]">
                <option value="">Todos os status</option>
                <option value="lead">Lead</option>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
            </select>
        </div>

        {{-- Tabela --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Empresa</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Atendimentos</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($clients as $client)
                        <tr class="text-sm text-gray-700 dark:text-gray-300">
                            <td class="px-4 py-3">
                                <a href="{{ route('clients.show', $client) }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">{{ $client->name }}</a>
                                @if ($client->email)<div class="text-xs text-gray-400">{{ $client->email }}</div>@endif
                            </td>
                            <td class="px-4 py-3">{{ $client->company ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badge[$client->status] }}">
                                    {{ \App\Models\Client::STATUS_LABELS[$client->status] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $client->interactions_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('clients.show', $client) }}" class="text-gray-500 hover:text-indigo-600">Ver</a>
                                <button wire:click="edit({{ $client->id }})" class="ml-3 text-gray-500 hover:text-indigo-600">Editar</button>
                                <button wire:click="delete({{ $client->id }})" wire:confirm="Remover este cliente?" class="ml-3 text-gray-500 hover:text-red-600">Excluir</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum cliente encontrado. Clique em <span class="font-semibold">+ Novo cliente</span> para começar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $clients->links() }}</div>
    </div>

    {{-- Modal de formulário --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="$set('showForm', false)">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $editingId ? 'Editar cliente' : 'Novo cliente' }}
                </h2>
                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nome *</label>
                        <input type="text" wire:model="name" class="{{ $input }}">
                        @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                            <input type="email" wire:model="email" class="{{ $input }}">
                            @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                            <input type="text" wire:model="phone" class="{{ $input }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Empresa</label>
                            <input type="text" wire:model="company" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select wire:model="status" class="{{ $input }}">
                                <option value="lead">Lead</option>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Anotações</label>
                        <textarea wire:model="notes" rows="3" class="{{ $input }}"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showForm', false)"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Cancelar</button>
                        <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
