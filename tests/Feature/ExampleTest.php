<?php

namespace Tests\Feature;

use App\Models\Box;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_boxes_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/boxes');
    }

    public function test_user_can_create_nested_box(): void
    {
        $root = Box::create([
            'code' => 'B001',
            'qr_uuid' => (string) Str::uuid(),
            'title' => 'Dokumen HR',
            'description' => 'Arsip kontrak kerja.',
            'level' => 1,
            'status' => 'packed',
        ]);

        $response = $this->post('/boxes', [
            'parent_id' => $root->id,
            'code' => 'B001-01',
            'title' => 'Kontrak 2025',
            'description' => 'Map kontrak tahun 2025.',
            'status' => 'packed',
        ]);

        $child = Box::where('code', 'B001-01')->first();

        $response->assertRedirect(route('boxes.show', $child));
        $this->assertSame($root->id, $child->parent_id);
        $this->assertSame(2, $child->level);
    }

    public function test_qr_uuid_opens_box_detail(): void
    {
        $box = Box::create([
            'code' => 'B001',
            'qr_uuid' => (string) Str::uuid(),
            'title' => 'Perlengkapan IT',
            'description' => 'Kabel dan adaptor.',
            'level' => 1,
            'status' => 'packed',
        ]);

        $this->get(route('boxes.qr.show', $box->qr_uuid))
            ->assertOk()
            ->assertSee('Perlengkapan IT');
    }
}
