@extends('admin.layout')

@section('title', 'LLM Usage Analytics')
@section('page-title', 'LLM Usage Analytics')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Usage Metrics</h3>
        <div class="flex gap-2">
            <a href="{{ route('admin.llm.usage', ['period' => 7]) }}" class="px-3 py-1 rounded-lg {{ $period == 7 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                7 Days
            </a>
            <a href="{{ route('admin.llm.usage', ['period' => 30]) }}" class="px-3 py-1 rounded-lg {{ $period == 30 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                30 Days
            </a>
            <a href="{{ route('admin.llm.usage', ['period' => 90]) }}" class="px-3 py-1 rounded-lg {{ $period == 90 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                90 Days
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500 text-sm font-medium">Total Cost</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($totalCost, 2) }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500 text-sm font-medium">Total Tokens</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalTokens) }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500 text-sm font-medium">API Calls</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $usages->total() }}</p>
    </div>
</div>

<!-- Daily Stats -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Costs</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Calls</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Input Tokens</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Output Tokens</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Cost</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($dailyStats as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ Carbon\Carbon::parse($stat->date)->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $stat->count }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($stat->input_tokens ?? 0) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($stat->output_tokens ?? 0) }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">${{ number_format($stat->cost ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">No usage data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Action Stats -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">By Action</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Calls</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total Cost</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Avg Response Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($actionStats as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $stat->action }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $stat->count }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">${{ number_format($stat->total_cost ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($stat->avg_response_time ?? 0) }}ms</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No usage data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Usages -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Recent API Calls</h3>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tokens</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Cost</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($usages as $usage)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $usage->action }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $usage->user?->name ?? 'System' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $usage->input_tokens + $usage->output_tokens }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ number_format($usage->cost, 4) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $usage->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $usage->success ? 'Success' : 'Failed' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $usage->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No usage data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $usages->links() }}
    </div>
</div>
@endsection
