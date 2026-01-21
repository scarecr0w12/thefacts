@extends('layout')

@section('title', 'Create Claim - VeriCrowd')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Submit a Claim</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('claims.store') }}" method="POST" class="bg-white p-8 rounded-lg border border-gray-200 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Claim (max 280 characters)</label>
            <textarea name="text" rows="4" maxlength="280" required placeholder="What claim do you want to fact-check?"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('text') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Clear, factual statements work best.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Context URL (optional)</label>
            <input type="url" name="context_url" value="{{ old('context_url') }}"
                placeholder="https://..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-medium">
                Create Claim
            </button>
            <a href="{{ route('claims.index') }}" class="border border-gray-300 px-6 py-2 rounded-md hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
