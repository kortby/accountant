<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->accountant = User::factory()->create([
        'role' => 'accountant',
    ]);
    $this->client = User::factory()->create([
        'role' => 'client',
    ]);
    $this->category = BlogCategory::create([
        'name' => 'Tax Tips',
        'slug' => 'tax-tips',
    ]);
});

function createBlogPost(array $overrides = []): BlogPost
{
    $defaults = [
        'user_id' => User::factory()->create(['role' => 'accountant'])->id,
        'title' => fake()->sentence(),
        'slug' => Str::slug(fake()->unique()->sentence()),
        'content' => fake()->paragraphs(3, true),
        'status' => 'published',
        'published_at' => now(),
    ];

    return BlogPost::create(array_merge($defaults, $overrides));
}

test('guests cannot access blog management page', function () {
    $this->get(route('blogs.index'))
        ->assertRedirect(route('login'));
});

test('clients cannot access blog management page', function () {
    $this->actingAs($this->client)
        ->get(route('blogs.index'))
        ->assertForbidden();
});

test('accountants can access blog management page', function () {
    $this->actingAs($this->accountant)
        ->get(route('blogs.index'))
        ->assertSuccessful();
});

test('blog management page displays posts', function () {
    $post = createBlogPost([
        'user_id' => $this->accountant->id,
        'title' => 'My Test Blog Post',
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->accountant)
        ->get(route('blogs.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Blog/Manage')
            ->has('posts.data', 1)
            ->where('posts.data.0.title', 'My Test Blog Post')
        );
});

test('blog management page can filter by status', function () {
    createBlogPost([
        'user_id' => $this->accountant->id,
        'title' => 'Published Post',
        'status' => 'published',
        'published_at' => now(),
        'category_id' => $this->category->id,
    ]);
    createBlogPost([
        'user_id' => $this->accountant->id,
        'title' => 'Draft Post',
        'status' => 'draft',
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->accountant)
        ->get(route('blogs.index', ['status' => 'draft']))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
            ->where('posts.data.0.title', 'Draft Post')
        );
});

test('blog management page can search posts', function () {
    createBlogPost([
        'user_id' => $this->accountant->id,
        'title' => 'Understanding Tax Deductions',
        'category_id' => $this->category->id,
    ]);
    createBlogPost([
        'user_id' => $this->accountant->id,
        'title' => 'Payroll Basics',
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->accountant)
        ->get(route('blogs.index', ['search' => 'Deductions']))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
            ->where('posts.data.0.title', 'Understanding Tax Deductions')
        );
});

test('blog management shows both drafts and published posts', function () {
    createBlogPost([
        'user_id' => $this->accountant->id,
        'status' => 'published',
        'published_at' => now(),
        'category_id' => $this->category->id,
    ]);
    createBlogPost([
        'user_id' => $this->accountant->id,
        'status' => 'draft',
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->accountant)
        ->get(route('blogs.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 2)
        );
});
