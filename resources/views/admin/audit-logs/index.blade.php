@extends('admin.layout')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Activity Log</h3>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Model</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Details</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $log->action === 'create' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $log->action === 'update' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $log->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $log->model }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $log->ip_address }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No audit logs found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $logs->links() }}
    </div>
</div>
@endsection
