@extends('layouts.app')

@section('title', 'Posts')

@section('content')
<div class="page-header">
    <h1>Posts</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary">+ New Post</a>
</div>

<div class="card">
    @if($posts->isEmpty())
        <p style="color:#888; text-align:center; padding: 2rem 0;">No posts yet. <a href="{{ route('posts.create') }}">Create the first one.</a></p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Published</th>
                    <th>Archived</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td><a href="{{ route('posts.show', $post) }}" style="color:#4f46e5; text-decoration:none; font-weight:500;">{{ $post->title }}</a></td>
                    <td>{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $post->is_archived ? 'badge-yes' : 'badge-no' }}">
                            {{ $post->is_archived ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $post->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
