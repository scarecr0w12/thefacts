@extends('admin.layout')

@section('title', 'Manage Claims')
@section('page-title', 'Claims Management')

@section('content')
<div class="mb-6">
    <a href="{{ route('claims.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Public</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">All Claims</h3>
        <span class="text-sm text-gray-500">{{ $claims->total() }} total</span>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Claim</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Author</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Verdict</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Evidence</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($claims as $claim)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($claim->text, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600">{{ $claim->creator->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $claim->verdict === 'TRUE' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $claim->verdict === 'FALSE' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $claim->verdict === 'MIXED' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $claim->verdict === 'UNVERIFIED' ? 'bg-gray-100 text-gray-800' : '' }}
                        ">
                            {{ $claim->verdict ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $claim->evidence->count() }} pieces</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $claim->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.claims.show', $claim) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No claims found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $claims->links() }}
    </div>
</div>
@endsection
