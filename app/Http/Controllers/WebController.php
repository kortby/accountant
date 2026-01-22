<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Mail\ContactFormConfirmation;
use App\Mail\ContactFormSubmission;
use App\Models\BlogPost;
use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WebController extends Controller
{
    public function home()
    {
        $testimonials = Testimonial::with('user')->get();
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
                        'role' => $post->author->role === 'accountant' ? 'Tax Expert' : 'Enrolled Agent & Tax Expert',
                        'imageUrl' => $post->author->profile_image ?? '/img/photos/' . rand(1, 3) . '.jpg',
                    ],
                ];
            });
        return Inertia::render('Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'latestBlogs' => $latestPosts,
            'testimonials' => $testimonials,
        ]);
    }

    public function taxService()
    {
        return Inertia::render('TaxServices');
    }

    public function aboutUs()
    {
        return Inertia::render('About');
    }

    public function contact()
    {
        return Inertia::render('Contact');
    }

    public function storeMessage(MessageRequest $request)
    {
        if (auth()->user()) {
            $request->merge(['user_id' => auth()->user()->id]);
        }

        $msg = Message::create($request->all());

        Mail::to('support@taxesaccountant.co')->send(
            new ContactFormSubmission(
                $msg->first_name ?? null,
                $msg->last_name ?? null,
                $msg->email ?? null,
                $msg->phone ?? null,
                $msg->content ?? null
            )
        );

        Mail::to($msg->email)->send(new ContactFormConfirmation($msg->first_name));

        $request->session()->flash('flash.banner', 'Your message has been sent!');
        return redirect('/');
    }
}
