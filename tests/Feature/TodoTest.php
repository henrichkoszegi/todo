<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_todos_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/todos');

        $response->assertOk();
    }

    public function test_unauthenticated_users_cannot_access_the_todos_page(): void
    {
        $response = $this->get('/todos');

        $response->assertRedirect('/login');
    }

    public function test_users_can_see_their_todos_on_the_todos_page(): void
    {
        $user = User::factory()->create();

        $foreignUser = User::factory()->create();

        $category = Category::factory()->create();

        $todo = $user->todos()->create([
            'name' => 'Test',
            'category_id' => $category->id,
            'shares' => [],
        ]);

        $foreignTodo = $foreignUser->todos()->create([
            'name' => 'Test',
            'category_id' => $category->id,
            'shares' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/todos');

        $response
            ->assertInertia(fn (Assert $page) => $page
                ->component('Todos/Index')
                ->has('todos.data', 1)
            );
    }

    public function test_create_todo_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/todos/create');

        $response->assertOk();
    }

    public function test_users_can_store_todo(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/todos/create')
            ->post('/todos', [
                'name' => 'Test',
                'category_id' => $category->id,
                'shares' => [],
            ]);

        $response
            ->assertSessionDoesntHaveErrors()
            ->assertSessionHas('success')
            ->assertRedirect('/todos');
    }

    public function test_users_can_delete_todo(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $todo = $user->todos()->create([
            'name' => 'Test',
            'category_id' => $category->id,
            'shares' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/todos/'.$todo->id.'/edit')
            ->delete('/todos/'.$todo->id);

        $response
            ->assertSessionHas('success')
            ->assertRedirect('/todos/'.$todo->id.'/edit');
    }

    public function test_users_cannot_delete_foreign_todo()
    {
        $user = User::factory()->create();

        $foreignUser = User::factory()->create();

        $category = Category::factory()->create();

        $foreignTodo = $foreignUser->todos()->create([
            'name' => 'Test',
            'category_id' => $category->id,
            'shares' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/todos/'.$foreignTodo->id.'/edit')
            ->delete('/todos/'.$foreignTodo->id);

        $response
            ->assertSessionHas('error')
            ->assertRedirect('/todos');
    }
}
