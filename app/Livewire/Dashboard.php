<?php

namespace App\Livewire;

use App\Models\Interaction;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Painel')]
class Dashboard extends Component
{
    public function render(): View
    {
        $clients = auth()->user()->clients();

        $byStatus = (clone $clients)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdue = (clone $clients)
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<', now())
            ->orderBy('next_followup_at')
            ->limit(8)
            ->get();

        $upcoming = (clone $clients)
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '>=', now())
            ->orderBy('next_followup_at')
            ->limit(8)
            ->get();

        $recent = Interaction::whereHas('client', fn ($q) => $q->where('user_id', auth()->id()))
            ->with('client')
            ->latest('happened_at')
            ->limit(6)
            ->get();

        return view('livewire.dashboard', [
            'total' => (clone $clients)->count(),
            'byStatus' => $byStatus,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            'recent' => $recent,
        ]);
    }
}
