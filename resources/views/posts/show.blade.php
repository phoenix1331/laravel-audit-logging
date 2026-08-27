@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="page-header">
    <h1>{{ $post->title }}</h1>
    <div class="actions">
        <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary">Edit</a>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

{{-- Post detail --}}
<div class="card" style="margin-bottom: 2rem;">
    @if($post->body)
        <p style="margin-bottom: 1.25rem; line-height: 1.7;">{{ $post->body }}</p>
    @else
        <p style="color:#9ca3af; margin-bottom: 1.25rem;">No body content.</p>
    @endif

    <div style="display:flex; gap:2rem; font-size:0.82rem; color:#6b7280; border-top:1px solid #f0f0f0; padding-top:1rem;">
        <span>
            <strong>Published:</strong>
            {{ $post->published_at?->format('d M Y H:i') ?? '—' }}
        </span>
        <span>
            <strong>Archived:</strong>
            <span class="badge {{ $post->is_archived ? 'badge-yes' : 'badge-no' }}" style="margin-left:0.2rem;">
                {{ $post->is_archived ? 'Yes' : 'No' }}
            </span>
        </span>
        <span><strong>Created:</strong> {{ $post->created_at->format('d M Y H:i') }}</span>
        <span><strong>Updated:</strong> {{ $post->updated_at->format('d M Y H:i') }}</span>
    </div>
</div>

{{-- Audit log timeline --}}
<h2>Audit Log</h2>

@if($logs->isEmpty())
    <div class="card">
        <p style="color:#9ca3af; text-align:center; padding:1rem 0;">No audit logs found for this post.</p>
    </div>
@else
    <div class="timeline">
        @foreach($logs as $log)
            @php
                $action = strtolower($log->action->value);
                $eventData = is_array($log->event_data) ? $log->event_data : [];
                $changes = collect($eventData)->except(['entity_id', 'object']);
            @endphp

            <div class="timeline-item">
                <div class="timeline-dot dot-{{ $action }}"></div>

                <div class="timeline-meta">
                    <span class="timeline-action action-{{ $action }}">{{ $log->action->value }}</span>
                    <span class="timeline-time">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y \a\t H:i:s') }}</span>
                </div>

                <div class="timeline-body">
                    @if($action === 'update' && $changes->isNotEmpty())
                        <table class="changes-table">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>From</th>
                                    <th>To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($changes as $field => $diff)
                                    <tr>
                                        <td class="field-name">{{ $field }}</td>
                                        <td class="val-from">
                                            @if(is_bool($diff['from']))
                                                {{ $diff['from'] ? 'true' : 'false' }}
                                            @elseif(is_null($diff['from']))
                                                <em style="color:#9ca3af">null</em>
                                            @else
                                                {{ $diff['from'] }}
                                            @endif
                                        </td>
                                        <td class="val-to">
                                            @if(is_bool($diff['to']))
                                                {{ $diff['to'] ? 'true' : 'false' }}
                                            @elseif(is_null($diff['to']))
                                                <em style="color:#9ca3af">null</em>
                                            @else
                                                {{ $diff['to'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($action === 'create')
                        <span style="color:#6b7280;">Post was created.</span>
                    @elseif($action === 'delete')
                        <span style="color:#6b7280;">Post was deleted.</span>
                    @endif

                    <div class="meta-row">
                        @if($log->ip_address)
                            <span>&#127760; {{ $log->ip_address }}</span>
                        @endif
                        @if($log->user_agent)
                            <span>&#128421; {{ $log->user_agent }}</span>
                        @endif
                        @if($log->request_route)
                            <span>&#128279; {{ $log->request_route }}</span>
                        @endif
                        @if($log->user_id)
                            <span>&#128100; User #{{ $log->user_id }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
