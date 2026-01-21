@extends('admin.layout')

@section('title', 'Claim Details')
@section('page-title', 'Claim: ' . Str::limit($claim->text, 50))

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.claims.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Claims</a>
    <form action="{{ route('admin.claims.destroy', $claim) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
            Delete Claim
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Claim Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Claim Details</h3>
            
            <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Claim Text</label>
                    <p class="text-gray-900">{{ $claim->text }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                    <p class="text-gray-900">{{ $claim->creator->name }} ({{ $claim->creator->email }})</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Context URL</label>
                    <p class="text-gray-900">{{ $claim->context_url ?? 'None' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Created At</label>
                    <p class="text-gray-900">{{ $claim->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Edit Verdict -->
            <form action="{{ route('admin.claims.update', $claim) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h4 class="font-semibold text-gray-900 mb-4">Edit Verdict</h4>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="verdict" class="block text-sm font-medium text-gray-700 mb-1">Verdict</label>
                        <select name="verdict" id="verdict" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Unverified</option>
                            <option value="TRUE" {{ $claim->verdict === 'TRUE' ? 'selected' : '' }}>True</option>
                            <option value="FALSE" {{ $claim->verdict === 'FALSE' ? 'selected' : '' }}>False</option>
                            <option value="MIXED" {{ $claim->verdict === 'MIXED' ? 'selected' : '' }}>Mixed</option>
                        </select>
                    </div>
                    <div>
                        <label for="confidence" class="block text-sm font-medium text-gray-700 mb-1">Confidence</label>
                        <input type="number" name="confidence" id="confidence" value="{{ $claim->confidence }}" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    Update Verdict
                </button>
            </form>
        </div>

        <!-- Evidence -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Evidence ({{ $claim->evidence->count() }})</h3>
            
            @forelse($claim->evidence as $evidence)
                <div class="pb-6 mb-6 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $evidence->title ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-500">{{ $evidence->publisher_domain ?? 'Unknown domain' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            {{ $evidence->stance === 'SUPPORTS' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $evidence->stance === 'REFUTES' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $evidence->stance === 'CONTEXT' ? 'bg-blue-100 text-blue-800' : '' }}
                        ">
                            {{ $evidence->stance }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $evidence->excerpt }}</p>
                    <p class="text-xs text-gray-500 mb-2">
                        <a href="{{ $evidence->url }}" target="_blank" class="text-blue-600 hover:underline">{{ Str::limit($evidence->url, 60) }}</a>
                    </p>
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Status: <span class="font-semibold">{{ $evidence->status }}</span></span>
                        <span>Votes: <span class="font-semibold">{{ $evidence->votes->sum('value') }}</span></span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No evidence submitted yet</p>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Stats -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h4 class="font-semibold text-gray-900 mb-4">Statistics</h4>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-600">Verdict</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $claim->verdict ?? 'Unverified' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Confidence</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $claim->confidence ?? '—' }}%</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Total Evidence</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $claim->evidence->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Ready Evidence</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $claim->evidence->where('status', 'READY')->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Total Votes</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $claim->evidence->sum(fn($e) => $e->votes->count()) }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
