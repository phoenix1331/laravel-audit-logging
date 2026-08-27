<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::latest()->get();

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $validated['is_archived'] = $request->boolean('is_archived');

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Post created.');
    }

    public function show(Post $post): View
    {
        $logs = AuditLog::where('event_data.entity_id', $post->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('posts.show', compact('post', 'logs'));
    }

    public function edit(Post $post): View
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $validated['is_archived'] = $request->boolean('is_archived');

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted.');
    }
}
