@extends('admin.layout')

@section('title', 'User Details')
@section('page-title', 'User: ' . $user->name)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Users</a>
    @if(auth()->id() !== $user->id)
        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                Delete User
            </button>
        </form>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- User Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Details</h3>
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ $user->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ $user->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm font-medium text-gray-700">Admin Access</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Verified</label>
                        <p class="text-gray-900">{{ $user->email_verified_at ? 'Yes (' . $user->email_verified_at->format('M d, Y') . ')' : 'No' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Joined</label>
                        <p class="text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    Update User
                </button>
            </form>
        </div>

        <!-- Activity -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity</h3>
            <div class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-600">Claims Created</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $user->claims->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Evidence Submitted</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $user->evidence->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Votes Cast</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $user->votes->count() }}</dd>
                </div>
            </div>
        </div>

        <!-- Recent Claims -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Claims</h3>
            @forelse($user->claims->take(5) as $claim)
                <div class="pb-4 mb-4 border-b border-gray-200 last:border-b-0">
                    <a href="{{ route('admin.claims.show', $claim) }}" class="text-blue-600 hover:underline font-medium">
                        {{ Str::limit($claim->text, 60) }}
                    </a>
                    <p class="text-sm text-gray-500">{{ $claim->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-gray-500">No claims yet</p>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=100" alt="Avatar" class="w-24 h-24 rounded-full mx-auto mb-2">
                <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
            <div class="border-t border-gray-200 pt-4">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-600 uppercase font-semibold">Status</dt>
                        <dd class="font-semibold text-gray-900">
                            {{ $user->is_admin ? '👑 Admin' : '👤 User' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-600 uppercase font-semibold">Member Since</dt>
                        <dd class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
