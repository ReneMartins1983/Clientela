<?php

namespace Tests\Feature;

use App\Livewire\ClientShow;
use App\Livewire\Dashboard;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardFollowupTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/dashboard')->assertOk()->assertSeeLivewire(Dashboard::class);
    }

    public function test_dashboard_lists_overdue_followups(): void
    {
        $user = User::factory()->create();
        Client::factory()->for($user)->create(['name' => 'Atrasado SA', 'next_followup_at' => now()->subDay()]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Atrasado SA');
    }

    public function test_owner_can_schedule_followup(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create(['next_followup_at' => null]);

        Livewire::actingAs($user)
            ->test(ClientShow::class, ['client' => $client])
            ->set('followup_at', now()->addDays(3)->format('Y-m-d\TH:i'))
            ->call('saveFollowup')
            ->assertHasNoErrors();

        $this->assertNotNull($client->fresh()->next_followup_at);
    }

    public function test_owner_can_clear_followup(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create(['next_followup_at' => now()->addDay()]);

        Livewire::actingAs($user)
            ->test(ClientShow::class, ['client' => $client])
            ->call('clearFollowup');

        $this->assertNull($client->fresh()->next_followup_at);
    }

    public function test_overdue_helper(): void
    {
        $past = Client::factory()->make(['next_followup_at' => now()->subHour()]);
        $future = Client::factory()->make(['next_followup_at' => now()->addHour()]);
        $none = Client::factory()->make(['next_followup_at' => null]);

        $this->assertTrue($past->isFollowupOverdue());
        $this->assertFalse($future->isFollowupOverdue());
        $this->assertFalse($none->isFollowupOverdue());
    }
}
