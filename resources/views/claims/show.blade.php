@extends('layout')

@section('title', $claim->text . ' - VeriCrowd')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Claim Header -->
    <div class="bg-white p-8 rounded-lg border border-gray-200 mb-8">
        <div class="flex items-start justify-between mb-4">
            <h1 class="text-2xl font-bold flex-1">{{ $claim->text }}</h1>
            <span class="inline-block px-4 py-2 text-sm font-semibold text-white rounded-full 
                @if ($claim->verdict === 'TRUE') bg-green-600
                @elseif ($claim->verdict === 'FALSE') bg-red-600
                @elseif ($claim->verdict === 'MIXED') bg-yellow-600
                @else bg-gray-600
                @endif">
                {{ $claim->verdict }}
            </span>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Confidence</p>
                <p class="text-2xl font-bold">{{ $claim->confidence }}%</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Evidence Items</p>
                <p class="text-2xl font-bold">{{ $claim->evidence->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Submitted By</p>
                <p class="text-sm font-medium">{{ $claim->creator->name }}</p>
                <p class="text-xs text-gray-500">{{ $claim->created_at->diffForHumans() }}</p>
            </div>
        </div>

        @if ($claim->context_url)
            <p class="text-sm text-gray-600">
                Context: <a href="{{ $claim->context_url }}" target="_blank" class="text-blue-600 hover:underline break-all">
                    {{ $claim->context_url }}
                </a>
            </p>
        @endif
    </div>

    <!-- Evidence Section -->
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-6">Evidence ({{ $claim->evidence->count() }})</h2>

        @forelse ($claim->evidence as $evidence)
            <div class="bg-white p-6 rounded-lg border border-gray-200 mb-4">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        @if ($evidence->title)
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $evidence->title }}</h3>
                        @endif
                        <a href="{{ $evidence->url }}" target="_blank" class="text-blue-600 hover:underline text-sm break-all">
                            {{ $evidence->publisher_domain ?? parse_url($evidence->url, PHP_URL_HOST) }}
                        </a>
                    </div>
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                        @if ($evidence->stance === 'SUPPORTS') bg-green-600
                        @elseif ($evidence->stance === 'REFUTES') bg-red-600
                        @else bg-blue-600
                        @endif">
                        {{ $evidence->stance }}
                    </span>
                </div>

                <p class="text-gray-700 text-sm mb-4 p-3 bg-gray-50 rounded">{{ $evidence->excerpt }}</p>

                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div>
                        @if ($evidence->published_at)
                            <span>Published: {{ $evidence->published_at->format('M d, Y') }}</span>
                        @endif
                        <span class="mx-2">·</span>
                        <span>Added by {{ $evidence->creator->name }} {{ $evidence->created_at->diffForHumans() }}</span>
                        @if ($evidence->status === 'PENDING')
                            <span class="ml-2 text-orange-600">⏳ Processing...</span>
                        @elseif ($evidence->status === 'FAILED')
                            <span class="ml-2 text-red-600">❌ {{ $evidence->error }}</span>
                        @endif
                    </div>
                </div>

                @if (auth()->check() && $evidence->status === 'READY')
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-3">Rate this evidence:</p>
                        <form action="{{ route('votes.store', $evidence) }}" method="POST" class="flex gap-2">
                            @csrf
                            <button name="value" value="1" type="submit" 
                                class="px-4 py-2 rounded border border-green-300 text-green-700 hover:bg-green-50 
                                    @if ($evidence->getUserVote(auth()->id())?->value === 1) bg-green-100 @endif">
                                👍 Helpful
                            </button>
                            <button name="value" value="-1" type="submit" 
                                class="px-4 py-2 rounded border border-red-300 text-red-700 hover:bg-red-50 
                                    @if ($evidence->getUserVote(auth()->id())?->value === -1) bg-red-100 @endif">
                                👎 Not Helpful
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-3 text-sm text-gray-600">
                    <span class="font-semibold">{{ $evidence->getVoteSum() }}</span> votes
                </div>
            </div>
        @empty
            <p class="text-gray-600">No evidence submitted yet.</p>
        @endforelse
    </div>

    <!-- Add Evidence Form -->
    @if (auth()->check())
        <div class="bg-white p-8 rounded-lg border border-gray-200">
            <h2 class="text-xl font-bold mb-6">Submit Evidence</h2>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('evidence.store', $claim) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                    <input type="url" name="url" value="{{ old('url') }}" required placeholder="https://..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stance</label>
                    <select name="stance" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select --</option>
                        <option value="SUPPORTS">Supports the claim</option>
                        <option value="REFUTES">Refutes the claim</option>
                        <option value="CONTEXT">Provides context</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt / Quote</label>
                    <textarea name="excerpt" rows="4" required placeholder="Copy a relevant quote or excerpt from the article..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('excerpt') }}</textarea>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-medium">
                    Submit Evidence
                </button>
            </form>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg text-center">
            <p class="text-blue-900 mb-4">Want to help verify this claim?</p>
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Login to submit evidence
            </a>
        </div>
    @endif
</div>
@endsection
