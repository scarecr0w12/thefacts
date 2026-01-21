@extends('admin.layout')

@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Logs</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Log Details</h3>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($log->action) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <p class="text-gray-900 font-semibold">{{ $log->model }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model ID</label>
                        <p class="text-gray-900 font-semibold">{{ $log->model_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <p class="text-gray-900 font-semibold">{{ $log->user?->name ?? 'System' }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                    <p class="text-gray-900 font-mono">{{ $log->ip_address }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Agent</label>
                    <p class="text-gray-900 text-sm break-all">{{ $log->user_agent }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timestamp</label>
                    <p class="text-gray-900 font-semibold">{{ $log->created_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        @if($log->before || $log->after)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Changes</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if($log->before)
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Before</h4>
                            <pre class="bg-gray-50 p-4 rounded-lg text-sm overflow-auto max-h-64 text-gray-900">{{ json_encode($log->before, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif

                    @if($log->after)
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">After</h4>
                            <pre class="bg-gray-50 p-4 rounded-lg text-sm overflow-auto max-h-64 text-gray-900">{{ json_encode($log->after, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-gray-900 mb-4">Summary</h4>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Action Type</dt>
                    <dd class="text-sm text-gray-900 mt-1">{{ ucfirst($log->action) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Affected Model</dt>
                    <dd class="text-sm text-gray-900 mt-1">{{ $log->model }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Record ID</dt>
                    <dd class="text-sm text-gray-900 mt-1">{{ $log->model_id ?? '—' }}</dd>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Changed By</dt>
                    <dd class="text-sm text-gray-900 mt-1">{{ $log->user?->name ?? 'System' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Time</dt>
                    <dd class="text-sm text-gray-900 mt-1">{{ $log->created_at->format('M d, Y H:i:s') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
