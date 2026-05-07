<?php

namespace Tests\Feature;

use App\Models\Box;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoxManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_splits_scan_and_management_pages(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('boxes.scan'))
            ->assertSee(route('boxes.index'));
    }

    public function test_header_navigation_changes_by_section(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('<a class="btn btn-sm btn-outline-secondary" href="'.route('boxes.scan').'">', false)
            ->assertDontSee('<a class="btn btn-sm btn-outline-primary" href="'.route('boxes.index').'">', false);

        $this->get(route('boxes.scan'))
            ->assertOk()
            ->assertSee('<a class="btn btn-sm btn-outline-primary" href="'.route('boxes.index').'">', false)
            ->assertDontSee('<a class="btn btn-sm btn-outline-secondary" href="'.route('boxes.scan').'">', false);

        $this->get(route('boxes.index'))
            ->assertOk()
            ->assertSee('<a class="btn btn-sm btn-outline-secondary" href="'.route('boxes.scan').'">', false)
            ->assertDontSee('<a class="btn btn-sm btn-outline-primary" href="'.route('boxes.index').'">', false);
    }

    public function test_child_can_move_to_a_different_parent_without_renaming_code(): void
    {
        $oldParent = Box::factory()->create(['code' => 'BOX001']);
        $newParent = Box::factory()->create(['code' => 'BOX002']);
        $child = Box::factory()->childOf($oldParent)->create(['code' => 'BOX003']);

        $this->put(route('boxes.update', $child), [
            'parent_id' => $newParent->id,
            'code' => 'BOX003',
            'description' => $child->description,
            'status' => $child->status,
        ])->assertRedirect(route('boxes.show', $child));

        $child->refresh();

        $this->assertSame($newParent->id, $child->parent_id);
        $this->assertSame('BOX003', $child->code);
        $this->assertSame(2, $child->level);
    }

    public function test_box_cannot_be_moved_under_its_descendant(): void
    {
        $root = Box::factory()->create(['code' => 'BOX001']);
        $child = Box::factory()->childOf($root)->create(['code' => 'BOX002']);
        $grandchild = Box::factory()->childOf($child)->create(['code' => 'BOX003']);

        $this->from(route('boxes.edit', $root))
            ->put(route('boxes.update', $root), [
                'parent_id' => $grandchild->id,
                'code' => $root->code,
                'description' => $root->description,
                'status' => $root->status,
            ])
            ->assertRedirect(route('boxes.edit', $root))
            ->assertSessionHasErrors('parent_id');

        $this->assertNull($root->refresh()->parent_id);
    }

    public function test_moving_status_is_not_allowed(): void
    {
        $box = Box::factory()->create(['code' => 'BOX001']);

        $this->from(route('boxes.edit', $box))
            ->put(route('boxes.update', $box), [
                'code' => $box->code,
                'description' => $box->description,
                'status' => 'moving',
            ])
            ->assertRedirect(route('boxes.edit', $box))
            ->assertSessionHasErrors('status');

        $this->assertSame('packed', $box->refresh()->status);
    }
}
