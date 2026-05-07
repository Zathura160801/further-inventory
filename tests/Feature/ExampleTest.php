<?php

namespace Tests\Feature;

use App\Models\Box;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_entry_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_user_can_create_child_box_with_independent_code(): void
    {
        $root = Box::create([
            'code' => 'B001',
            'qr_uuid' => (string) Str::uuid(),
            'description' => 'Arsip kontrak kerja.',
            'level' => 1,
            'status' => 'packed',
        ]);

        $response = $this->post('/boxes', [
            'parent_id' => $root->id,
            'code' => 'B002',
            'description' => 'Map kontrak tahun 2025.',
            'status' => 'packed',
        ]);

        $box = Box::where('code', 'B002')->first();

        $response->assertRedirect(route('boxes.show', $box));
        $this->assertSame($root->id, $box->parent_id);
        $this->assertSame(2, $box->level);
        $this->assertSame('Map kontrak tahun 2025.', $box->description);
    }

    public function test_qr_uuid_opens_box_detail(): void
    {
        $box = Box::create([
            'code' => 'B001',
            'qr_uuid' => (string) Str::uuid(),
            'description' => 'Kabel dan adaptor.',
            'status' => 'packed',
        ]);

        $this->get(route('boxes.qr.show', $box->qr_uuid))
            ->assertOk()
            ->assertSee('B001')
            ->assertSee(__('boxes.save_notes'))
            ->assertDontSee(__('boxes.download_qr'));
    }

    public function test_scanned_box_notes_can_be_updated_without_management_fields(): void
    {
        $box = Box::factory()->create([
            'code' => 'B001',
            'description' => 'Old note.',
        ]);

        $this->patch(route('boxes.notes.update', $box), [
            'description' => 'Updated from scan page.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Updated from scan page.', $box->refresh()->description);
    }
}
