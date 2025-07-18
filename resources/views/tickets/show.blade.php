@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Ticket #{{ $ticket->ticket_number }}: {{ $ticket->title }}</h2>
                        <div>
                            <span class="badge badge-{{ 
                                $ticket->priority == 'Low' ? 'info' : 
                                ($ticket->priority == 'Medium' ? 'primary' : 
                                ($ticket->priority == 'High' ? 'warning' : 'danger')) 
                            }}">
                                {{ $ticket->priority }}
                            </span>
                            <span class="badge badge-{{ 
                                $ticket->status == 'Open' ? 'primary' : 
                                ($ticket->status == 'In Progress' ? 'warning' : 
                                ($ticket->status == 'On Hold' ? 'secondary' : 
                                ($ticket->status == 'Resolved' ? 'success' : 'dark'))) 
                            }}">
                                {{ $ticket->status }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Created By:</strong> {{ $ticket->user->name }}</p>
                                    <p><strong>Created At:</strong> {{ $ticket->created_at->format('Y-m-d H:i') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $ticket->updated_at->format('Y-m-d H:i') }}</p>
                                    @if($ticket->resolved_at)
                                        <p><strong>Resolved At:</strong> {{ $ticket->resolved_at->format('Y-m-d H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Assignment</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Assigned To:</strong> 
                                        @if($ticket->assignedTo)
                                            {{ $ticket->assignedTo->name }}
                                        @else
                                            Unassigned
                                        @endif
                                    </p>
                                    <p><strong>Related Asset:</strong> 
                                        @if($ticket->asset)
                                            <a href="{{ route('assets.show', $ticket->asset) }}">
                                                {{ $ticket->asset->asset_tag }} - {{ $ticket->asset->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Description</h5>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>
                    </div>
                    
                    @if($ticket->resolution)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Resolution</h5>
                            </div>
                            <div class="card-body">
                                {!! nl2br(e($ticket->resolution)) !!}
                            </div>
                        </div>
                    @endif
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Comments</h5>
                        </div>
                        <div class="card-body">
                            @if($ticket->comments->count() > 0)
                                @foreach($ticket->comments as $comment)
                                    <div class="media mb-4">
                                        <div class="media-body">
                                            <h6 class="mt-0">{{ $comment->user->name }}
                                                <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                                            </h6>
                                            {!! nl2br(e($comment->comment)) !!}
                                        </div>
                                    </div>
                                    @if(!$loop->last)
                                        <hr>
                                    @endif
                                @endforeach
                            @else
                                <p>No comments yet.</p>
                            @endif
                            
                            <form action="{{ route('tickets.comment', $ticket) }}" method="POST" class="mt-4">
                                @csrf
                                <div class="form-group">
                                    <label for="comment">Add Comment</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" 
                                              name="comment" rows="3" required></textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Comment</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if(in_array($ticket->status, ['Open', 'In Progress', 'On Hold']))
                        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('tickets.resolve', $ticket) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Mark as Resolved
                                </button>
                            </form>
                        @endif
                    @endif
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection