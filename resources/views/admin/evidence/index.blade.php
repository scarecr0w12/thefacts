@extends('admin.layout')

@section('title', 'Manage Evidence')
@section('page-title', 'Evidence Management')

@section('content')
<div class="mb-6">
    <a href="{{ route('claims.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Public</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">All Evidence</h3>
        <span class="text-sm text-gray-500">{{ $evidence->total() }} total</span>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Claim</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stance</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Domain</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Votes</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($evidence as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($item->claim->text, 40) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $item->stance === 'SUPPORTS' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $item->stance === 'REFUTES' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $item->stance === 'CONTEXT' ? 'bg-blue-100 text-blue-800' : '' }}
                        ">
                            {{ $item->stance }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $item->status === 'READY' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $item->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $item->status === 'FAILED' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600">{{ $item->publisher_domain ?? 'Unknown' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600">{{ $item->votes->sum('value') ?? 0 }}</div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.evidence.show', $item) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No evidence found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $evidence->links() }}
    </div>
</div>
@endsection
