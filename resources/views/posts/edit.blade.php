@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="page-header">
    <h1>Edit Post <span style="color:#888; font-weight:400; font-size:1rem">#{{ $post->id }}</span></h1>
    <a href="{{ route('posts.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('posts.update', $post) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title <span style="color:#ef4444">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" autofocus>
            @error('title')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="body">Body</label>
            <textarea id="body" name="body">{{ old('body', $post->body) }}</textarea>
            @error('body')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="published_at">Publish Date</label>
            <input type="datetime-local" id="published_at" name="published_at"
                value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
            @error('published_at')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <div class="checkbox-row">
                <input type="checkbox" id="is_archived" name="is_archived" value="1"
                    {{ old('is_archived', $post->is_archived) ? 'checked' : '' }}>
                <label for="is_archived" style="margin:0">Archived</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
