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

    /**
     * Test the store method of TagController.
     *
     * @return void
     */
    public function test_store_creates_a_new_tag()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'New Tag',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson(route('tag.store'), $payload);

        $response->assertStatus(201); // 201 Created

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'New Tag',
            'user_id' => $user->id,
        ]);

        $this->assertEquals('New Tag', $response->json('data.name'));
    }

    /**
     * Test the store method validation.
     *
     * @return void
     */
    public function test_store_validation_errors()
    {
        $user = User::factory()->create();

        // Attempt to create a tag without a name
        $payload = [];

        $response = $this->actingAs($user, 'sanctum')->postJson(route('tag.store'), $payload);

        $response->assertStatus(422); // 422 Unprocessable Entity

        $response->assertJsonValidationErrors(['name']);
    }

    /**
     * Test unauthorized access to the store method.
     *
     * @return void
     */
    public function test_store_requires_authentication()
    {
        $payload = [
            'name' => 'Unauthorized Tag',
        ];

        $response = $this->postJson(route('tag.store'), $payload);

        $response->assertStatus(401); // 401 Unauthorized
    }

    /**
     * Test storing a tag with a duplicate name for the same user.
     *
     * @return void
     */
    public function test_store_with_duplicate_tag_name()
    {
        $user = User::factory()->create();

        // Create an initial tag
        Tag::factory()->create([
            'name' => 'Duplicate Tag',
            'user_id' => $user->id,
        ]);

        // Attempt to create another tag with the same name
        $payload = [
            'name' => 'Duplicate Tag',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson(route('tag.store'), $payload);

        $response->assertStatus(422); // 422 Unprocessable Entity

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_show_returns_specific_tag()
    {
        $user = User::factory()->create();

        // Create a tag for the user
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act as the user and request the specific tag
        $response = $this->actingAs($user, 'sanctum')->getJson(route('tag.show', $tag->sqid));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'id' => $tag->sqid,
                'name' => $tag->name,
            ],
        ]);
    }
}
