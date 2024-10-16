<?php

namespace Tests\Feature\Controller;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method of TagController.
     *
     * @return void
     */
    public function test_index_returns_paginated_tags_for_authenticated_user()
    {
        $user = User::factory()->create();

        Tag::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);

        Tag::factory()->count(5)->create();

        $limit = 10;
        $page = 1;

        $response = $this->actingAs($user, 'sanctum')->getJson(route('tag.index', [
            'limit' => $limit,
            'page' => $page,
        ]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
            'links',
            'meta',
        ]);


        $this->assertCount($limit, $response->json('data'));
        foreach ($response->json('data') as $tag) {
            $this->assertEquals($user->id, Tag::findBySqid($tag['id'])->user_id);
        }
    }
}
