<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Clientes')]
class Clients extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public bool $overdueOnly = false;

    // formulário (modal)
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $status = 'lead';
    public string $notes = '';
    public string $next_followup_at = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:lead,active,inactive'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_followup_at' => ['nullable', 'date'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedOverdueOnly(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'email', 'phone', 'company', 'notes', 'next_followup_at']);
        $this->status = 'lead';
        $this->resetValidation();
        $this->showForm = true;
    }

    public function edit(Client $client): void
    {
        $this->ensureOwner($client);
        $this->editingId = $client->id;
        $this->name = $client->name;
        $this->email = (string) $client->email;
        $this->phone = (string) $client->phone;
        $this->company = (string) $client->company;
        $this->status = $client->status;
        $this->notes = (string) $client->notes;
        $this->next_followup_at = $client->next_followup_at?->format('Y-m-d\TH:i') ?? '';
        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['next_followup_at'] = $this->next_followup_at ?: null;

        if ($this->editingId) {
            $client = Client::findOrFail($this->editingId);
            $this->ensureOwner($client);
            $client->update($data);
        } else {
            auth()->user()->clients()->create($data);
        }

        $this->showForm = false;
        $this->dispatch('saved');
    }

    public function delete(Client $client): void
    {
        $this->ensureOwner($client);
        $client->delete();
    }

    private function ensureOwner(Client $client): void
    {
        abort_unless($client->user_id === auth()->id(), 403);
    }

    public function render(): View
    {
        $clients = auth()->user()->clients()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('company', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->overdueOnly, fn ($q) => $q->whereNotNull('next_followup_at')->where('next_followup_at', '<', now()))
            ->withCount('interactions')
            ->latest()
            ->paginate(10);

        return view('livewire.clients', compact('clients'));
    }
}
