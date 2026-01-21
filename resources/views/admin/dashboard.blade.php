@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
            </div>
            <div class="text-4xl text-blue-500">👥</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Claims</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_claims'] }}</p>
            </div>
            <div class="text-4xl text-green-500">📋</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Evidence</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_evidence'] }}</p>
            </div>
            <div class="text-4xl text-purple-500">📑</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Votes</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_votes'] }}</p>
            </div>
            <div class="text-4xl text-orange-500">🗳️</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Evidence Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Evidence Status</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Pending</span>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full font-semibold">{{ $stats['evidence_pending'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Ready</span>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold">{{ $stats['evidence_ready'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Failed</span>
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full font-semibold">{{ $stats['evidence_failed'] }}</span>
            </div>
        </div>
    </div>

    <!-- Verdict Breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verdict Breakdown</h3>
        <div class="space-y-3">
            @foreach(['TRUE' => 'green', 'FALSE' => 'red', 'MIXED' => 'yellow', 'UNVERIFIED' => 'gray'] as $verdict => $color)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $verdict }}</span>
                    <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 rounded-full font-semibold">
                        {{ $verdictBreakdown->get($verdict, 0) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- This Month -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">This Month</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">New Claims</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">{{ $stats['claims_this_month'] }}</span>
            </div>
            <div class="pt-3 border-t border-gray-200 text-xs text-gray-500">
                Last updated: {{ now()->format('M d, Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Recent Sections -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Claims -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Claims</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($recentClaims as $claim)
                <a href="{{ route('admin.claims.show', $claim) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                    <p class="text-sm font-medium text-gray-900">{{ Str::limit($claim->text, 60) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        by {{ $claim->creator->name }} • {{ $claim->created_at->diffForHumans() }}
                    </p>
                </a>
            @endforeach
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.claims.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                View all claims →
            </a>
        </div>
    </div>

    <!-- Recent Evidence -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Evidence</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($recentEvidence as $evidence)
                <a href="{{ route('admin.evidence.show', $evidence) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                    <p class="text-sm font-medium text-gray-900">{{ $evidence->claim->text }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $evidence->stance }} • {{ $evidence->created_at->diffForHumans() }}
                    </p>
                </a>
            @endforeach
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.evidence.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                View all evidence →
            </a>
        </div>
    </div>
</div>
@endsection
