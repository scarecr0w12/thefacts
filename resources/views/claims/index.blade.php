@extends('layout')

@section('title', 'Claims - VeriCrowd')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Claims</h1>
        @if (auth()->check())
            <a href="{{ route('claims.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + New Claim
            </a>
        @endif
    </div>

    <form action="{{ route('claims.index') }}" method="GET" class="flex gap-2 mb-6">
        <input type="text" name="q" placeholder="Search claims..." value="{{ request('q') }}"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Search
        </button>
    </form>
</div>

@if ($claims->count() > 0)
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($claims as $claim)
            <a href="{{ route('claims.show', $claim) }}" class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition">
                <div class="mb-3">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-white rounded-full 
                        @if ($claim->verdict === 'TRUE') bg-green-600
                        @elseif ($claim->verdict === 'FALSE') bg-red-600
                        @elseif ($claim->verdict === 'MIXED') bg-yellow-600
                        @else bg-gray-600
                        @endif">
                        {{ $claim->verdict }}
                    </span>
                    <span class="text-xs text-gray-500 ml-2">Confidence: {{ $claim->confidence }}%</span>
                </div>
                <p class="text-gray-900 font-medium mb-2 line-clamp-3">{{ $claim->text }}</p>
                <div class="flex items-center text-sm text-gray-600">
                    <span>{{ $claim->evidence_count ?? $claim->evidence()->count() }} evidence</span>
                    <span class="mx-2">·</span>
                    <span>{{ $claim->created_at->diffForHumans() }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $claims->links() }}
    </div>
@else
    <div class="text-center py-12">
        <p class="text-gray-600 mb-4">No claims found</p>
        @if (auth()->check())
            <a href="{{ route('claims.create') }}" class="text-blue-600 hover:underline">Create the first claim</a>
        @endif
    </div>
@endif
@endsection
