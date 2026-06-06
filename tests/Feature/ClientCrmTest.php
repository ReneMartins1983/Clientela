<?php

namespace Tests\Feature;

use App\Livewire\ClientShow;
use App\Livewire\Clients;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_page_requires_authentication(): void
    {
        $this->get('/clients')->assertRedirect('/login');
    }

    public function test_authenticated_user_sees_clients_page(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/clients')->assertOk()->assertSeeLivewire(Clients::class);
    }

    public function test_user_can_create_a_client(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Clients::class)
            ->call('create')
            ->set('name', 'Acme Ltda')
            ->set('email', 'contato@acme.com')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'name' => 'Acme Ltda',
            'status' => 'active',
        ]);
    }

    public function test_client_name_is_required(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Clients::class)
            ->call('create')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_list_shows_only_own_clients(): void
    {
        $user = User::factory()->create();
        Client::factory()->for($user)->create(['name' => 'Meu Cliente']);
        Client::factory()->create(['name' => 'Cliente Alheio']);

        Livewire::actingAs($user)
            ->test(Clients::class)
            ->assertSee('Meu Cliente')
            ->assertDontSee('Cliente Alheio');
    }

    public function test_user_cannot_open_others_client(): void
    {
        $other = Client::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('clients.show', $other))
            ->assertForbidden();
    }

    public function test_owner_can_add_interaction(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(ClientShow::class, ['client' => $client])
            ->set('type', 'call')
            ->set('notes', 'Liguei para o cliente')
            ->set('happened_at', now()->format('Y-m-d\TH:i'))
            ->call('addInteraction')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('interactions', [
            'client_id' => $client->id,
            'type' => 'call',
            'notes' => 'Liguei para o cliente',
        ]);
    }

    public function test_owner_can_delete_a_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Clients::class)
            ->call('delete', $client)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
