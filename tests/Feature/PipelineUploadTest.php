<?php

namespace Tests\Feature;

use App\Livewire\ClientShow;
use App\Livewire\Pipeline;
use App\Models\Attachment;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PipelineUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_move_client_between_stages(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create(['status' => 'lead']);

        Livewire::actingAs($user)
            ->test(Pipeline::class)
            ->call('moveClient', $client->id, 'active');

        $this->assertSame('active', $client->fresh()->status);
    }

    public function test_move_ignores_invalid_status(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create(['status' => 'lead']);

        Livewire::actingAs($user)
            ->test(Pipeline::class)
            ->call('moveClient', $client->id, 'won');

        $this->assertSame('lead', $client->fresh()->status);
    }

    public function test_user_cannot_move_others_client(): void
    {
        $client = Client::factory()->create(['status' => 'lead']); // de outro dono

        Livewire::actingAs(User::factory()->create())
            ->test(Pipeline::class)
            ->call('moveClient', $client->id, 'active');

        $this->assertSame('lead', $client->fresh()->status);
    }

    public function test_owner_can_upload_attachment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(ClientShow::class, ['client' => $client])
            ->set('upload', UploadedFile::fake()->create('proposta.pdf', 120, 'application/pdf'))
            ->call('saveUpload')
            ->assertHasNoErrors();

        $attachment = Attachment::first();
        $this->assertNotNull($attachment);
        $this->assertSame('proposta.pdf', $attachment->name);
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_attachment_download_requires_ownership(): void
    {
        $attachment = Attachment::factory()->create(); // de outro dono (via factory chain)

        $this->actingAs(User::factory()->create())
            ->get(route('attachments.download', $attachment))
            ->assertForbidden();
    }
}
