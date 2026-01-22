<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


class BlogPostController extends Controller
{
    /**
     * Display a listing of blog posts with pagination
     */
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author', 'tags'])
            ->where('status', 'published')
            ->latest('published_at');

        // Apply category filter
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Apply tag filter
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Apply search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(9)->through(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'featured_image' => Storage::url($post->featured_image) == '/storage/' ? '/img/photos/' . rand(1, 3) . '.jpg' : Storage::url($post->featured_image) ?? '/img/photos/' . rand(1, 3) . '.jpg',
                'published_at' => $post->published_at->format('M d, Y'),
                'reading_time' => $post->reading_time . ' min read',
                'category' => [
                    'name' => $post->category->name,
                    'slug' => $post->category->slug,
                ],
                'author' => [
                    'name' => $post->author->first_name . ' ' . $post->author->last_name,
                    'role' => $post->author->role === 'accountant' ? 'Tax Expert' : 'Enrolled Agent & Tax Expert',
                    'avatar' => $post->author->profile_image ?? '/img/photos/' . rand(1, 3) . '.jpg',
                ],
                'tags' => $post->tags->map(fn($tag) => [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]),
            ];
        });

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'category', 'tag']),
            'categories' => BlogCategory::select('name', 'slug')->get(),
            'tags' => BlogTag::select('name', 'slug')->get(),
        ]);
    }

    /**
     * Get latest blog posts for homepage
     */
    public function getLatestPosts()
    {
        $latestPosts = BlogPost::with(['category', 'author'])
            ->where('status', 'published')
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'href' => route('blog.show', $post->slug),
                    'description' => $post->excerpt,
                    'imageUrl' => Storage::url($post->featured_image) == '/storage/' ? '/img/photos/' . rand(1, 3) . '.jpg' : Storage::url($post->featured_image) ?? '/img/photos/' . rand(1, 3) . '.jpg',
                    'date' => $post->published_at->format('M d, Y'),
                    'datetime' => $post->published_at->toISOString(),
                    'category' => [
                        'title' => $post->category->name,
                        'href' => route('blog.category', $post->category->slug),
                    ],
                    'author' => [
                        'name' => $post->author->first_name . ' ' . $post->author->last_name,
                        'role' => $post->author->role === 'accountant' ? 'Enrolled Agent & Tax Expert' : 'Enrolled Agent & Tax Expert',
                        'imageUrl' => $post->author->profile_image ?? '/img/photos/' . rand(1, 3) . '.jpg',
                    ],
                ];
            });

        return response()->json($latestPosts);
    }

    /**
     * Show single blog post
     */
    public function show(BlogPost $blogPost)
    {

        // Increment view count
        $blogPost->increment('view_count');

        $data = [
            'post' => [
                'id' => $blogPost->id,
                'title' => $blogPost->title,
                'content' => $blogPost->content,
                'featured_image' => Storage::url($blogPost->featured_image) == '/storage/' ? '/img/photos/' . rand(1, 3) . '.jpg' : Storage::url($blogPost->featured_image),
                'published_at' => $blogPost->published_at->format('M d, Y'),
                'reading_time' => $blogPost->reading_time . ' min read',
                'view_count' => $blogPost->view_count,
                'category' => [
                    'name' => $blogPost->category->name,
                    'slug' => $blogPost->category->slug,
                ],
                'author' => [
                    'name' => $blogPost->author->first_name . ' ' . $blogPost->author->last_name,
                    'role' => $blogPost->author->role === 'accountant' ? 'Tax Expert' : 'Enrolled Agent & Tax Expert',
                    'avatar' => $blogPost->author->profile_image ?? '/img/photos/' . rand(1, 3) . '.jpg',
                    'bio' => $blogPost->author->bio ?? null,
                ],
                'tags' => $blogPost->tags->map(fn($tag) => [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]),
                'related_posts' => $blogPost->relatedPosts->map(fn($related) => [
                    'title' => $related->title,
                    'slug' => $related->slug,
                    'excerpt' => $related->excerpt,
                    'featured_image' => $related->featured_image ?? '/img/photos/' . rand(1, 3) . '.jpg',
                ]),
            ],
        ];


        return Inertia::render('BlogShow', $data);
    }

    /**
     * Show posts by category
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::with(['author', 'tags'])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        return Inertia::render('Blog/Category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    /**
     * Show posts by tag
     */
    public function tag($slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()
            ->with(['author', 'category'])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        return Inertia::render('Blog/Tag', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }

    /**
     * Show blog post creation form
     */
    public function create()
    {
        // $this->authorize('create', BlogPost::class);

        // return Inertia::render('Blog/Create', [
        //     'categories' => BlogCategory::select('id', 'name')->get(),
        //     'tags' => BlogTag::select('id', 'name')->get(),
        // ]);
    }

    /**
     * Store new blog post
     */
    public function store(Request $request)
    {
        // $this->authorize('create', BlogPost::class);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'required|max:500',
            'category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id',
            'status' => 'required|in:draft,published',
        ]);

        $post = new BlogPost($validated);
        $post->user_id = Auth::id();
        $post->slug = Str::slug($request->title);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog', 'public');
            $post->featured_image = Storage::url($path);
        }

        $post->save();

        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('blog.show', $post->slug)
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Show blog post edit form
     */
    public function edit(BlogPost $post)
    {
        // $this->authorize('update', $post);

        return Inertia::render('Blog/Edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'excerpt' => $post->excerpt,
                'category_id' => $post->category_id,
                'featured_image' => Storage::url($post->featured_image) == '/storage/' ? '/img/photos/' . rand(1, 3) . '.jpg' : Storage::url($post->featured_image),
                'status' => $post->status,
                'tags' => $post->tags->pluck('id'),
            ],
            'categories' => BlogCategory::select('id', 'name')->get(),
            'tags' => BlogTag::select('id', 'name')->get(),
        ]);
    }

    /**
     * Update blog post
     */
    public function update(Request $request, BlogPost $post)
    {
        // $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'required|max:500',
            'category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id',
            'status' => 'required|in:draft,published',
        ]);

        $post->fill($validated);

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            // if ($post->featured_image) {
            //     Storage::disk('public')->delete(str_replace('/storage/', '', $post->featured_image));
            // }

            $path = $request->file('featured_image')->store('blog', 'public');
            $post->featured_image = Storage::url($path);
        }

        $post->save();

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('blog.show', $post->slug)
            ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Delete blog post
     */
    public function destroy(BlogPost $post)
    {
        // $this->authorize('delete', $post);

        // Delete featured image if exists
        // if ($post->featured_image) {
        //     Storage::disk('public')->delete(str_replace('/storage/', '', $post->featured_image));
        // }

        // $post->delete();

        // return redirect()->route('blog.index')
        //     ->with('success', 'Blog post deleted successfully.');
    }
}
