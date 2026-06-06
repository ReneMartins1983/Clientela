<?php

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Client;
use App\Models\Interaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ClientShow extends Component
{
    use WithFileUploads;

    public Client $client;

    public string $type = 'note';
    public string $notes = '';
    public string $happened_at = '';

    public $upload;

    public function mount(Client $client): void
    {
        abort_unless($client->user_id === auth()->id(), 403);
        $this->client = $client;
        $this->happened_at = now()->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', 'in:call,email,meeting,whatsapp,note'],
            'notes' => ['required', 'string', 'max:2000'],
            'happened_at' => ['required', 'date'],
        ];
    }

    public function addInteraction(): void
    {
        $this->client->interactions()->create($this->validate());

        $this->reset('notes');
        $this->type = 'note';
        $this->happened_at = now()->format('Y-m-d\TH:i');
        $this->dispatch('interaction-added');
    }

    public function deleteInteraction(Interaction $interaction): void
    {
        abort_unless($interaction->client_id === $this->client->id, 403);
        $interaction->delete();
    }

    public function saveUpload(): void
    {
        $this->validate([
            'upload' => ['required', 'file', 'max:4096'], // até 4 MB
        ]);

        $path = $this->upload->store('attachments/'.$this->client->id, 'local');

        $this->client->attachments()->create([
            'name' => $this->upload->getClientOriginalName(),
            'path' => $path,
            'size' => $this->upload->getSize(),
            'mime' => $this->upload->getMimeType(),
        ]);

        $this->reset('upload');
    }

    public function deleteAttachment(Attachment $attachment): void
    {
        abort_unless($attachment->client_id === $this->client->id, 403);
        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();
    }

    public function render(): View
    {
        return view('livewire.client-show', [
            'interactions' => $this->client->interactions()->get(),
            'attachments' => $this->client->attachments()->get(),
        ]);
    }
}
