<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Funil')]
class Pipeline extends Component
{
    /** Move um cliente para outra etapa do funil (drag-and-drop). */
    public function moveClient(int $id, string $status): void
    {
        if (! in_array($status, Client::STATUSES, true)) {
            return;
        }

        $client = auth()->user()->clients()->find($id);

        if ($client && $client->status !== $status) {
            $client->update(['status' => $status]);
        }
    }

    public function render(): View
    {
        $groups = auth()->user()->clients()
            ->withCount('interactions')
            ->orderBy('name')
            ->get()
            ->groupBy('status');

        return view('livewire.pipeline', compact('groups'));
    }
}
