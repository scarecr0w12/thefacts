@extends('admin.layout')

@section('title', 'Evidence Details')
@section('page-title', 'Evidence Details')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.evidence.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Evidence</a>
    <form action="{{ route('admin.evidence.destroy', $evidence) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
            Delete Evidence
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Evidence Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Evidence Details</h3>
            
            <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Claim</label>
                    <a href="{{ route('admin.claims.show', $evidence->claim) }}" class="text-blue-600 hover:underline">
                        {{ $evidence->claim->text }}
                    </a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                    <a href="{{ $evidence->url }}" target="_blank" class="text-blue-600 hover:underline break-all">
                        {{ $evidence->url }}
                    </a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <p class="text-gray-900">{{ $evidence->title ?? 'Not extracted' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Domain</label>
                    <p class="text-gray-900">{{ $evidence->publisher_domain ?? 'Not extracted' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Published At</label>
                    <p class="text-gray-900">{{ $evidence->published_at?->format('M d, Y') ?? 'Unknown' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                    <p class="text-gray-900">{{ $evidence->excerpt }}</p>
                </div>
                @if($evidence->error)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <label class="block text-sm font-medium text-red-800 mb-1">Error Message</label>
                        <p class="text-sm text-red-700">{{ $evidence->error }}</p>
                    </div>
                @endif
            </div>

            <!-- Edit Evidence -->
            <form action="{{ route('admin.evidence.update', $evidence) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h4 class="font-semibold text-gray-900 mb-4">Edit Evidence</h4>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="stance" class="block text-sm font-medium text-gray-700 mb-1">Stance</label>
                        <select name="stance" id="stance" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="SUPPORTS" {{ $evidence->stance === 'SUPPORTS' ? 'selected' : '' }}>Supports</option>
                            <option value="REFUTES" {{ $evidence->stance === 'REFUTES' ? 'selected' : '' }}>Refutes</option>
                            <option value="CONTEXT" {{ $evidence->stance === 'CONTEXT' ? 'selected' : '' }}>Context</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="PENDING" {{ $evidence->status === 'PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="READY" {{ $evidence->status === 'READY' ? 'selected' : '' }}>Ready</option>
                            <option value="FAILED" {{ $evidence->status === 'FAILED' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    Update Evidence
                </button>
            </form>
        </div>

        <!-- Votes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Votes ({{ $evidence->votes->count() }})</h3>
            
            @forelse($evidence->votes as $vote)
                <div class="pb-4 mb-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900">{{ $vote->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $vote->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 {{ $vote->value > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full font-semibold">
                            {{ $vote->value > 0 ? '+1 Helpful' : '-1 Unhelpful' }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No votes yet</p>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-gray-900 mb-4">Statistics</h4>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-600">Stance</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $evidence->stance }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Status</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $evidence->status }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Votes</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $evidence->votes->sum('value') ?? 0 }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Created By</dt>
                    <dd class="text-sm text-gray-900">{{ $evidence->creator->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Created At</dt>
                    <dd class="text-sm text-gray-900">{{ $evidence->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
