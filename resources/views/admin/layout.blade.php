<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white overflow-y-auto">
            <div class="p-6 border-b border-gray-800">
                <h1 class="text-2xl font-bold">VeriCrowd Admin</h1>
            </div>

            <nav class="p-4">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : '' }}">
                    <span class="font-medium">Dashboard</span>
                </a>

                <div class="mt-8">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3">Content</h3>
                    <a href="{{ route('admin.claims.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.claims*') ? 'bg-blue-600' : '' }}">
                        Claims
                    </a>
                    <a href="{{ route('admin.evidence.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.evidence*') ? 'bg-blue-600' : '' }}">
                        Evidence
                    </a>
                </div>

                <div class="mt-8">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3">Users & Access</h3>
                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.users*') ? 'bg-blue-600' : '' }}">
                        Users
                    </a>
                </div>

                <div class="mt-8">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3">LLM & AI</h3>
                    <a href="{{ route('admin.llm.config') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.llm.config') ? 'bg-blue-600' : '' }}">
                        Configuration
                    </a>
                    <a href="{{ route('admin.llm.usage') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.llm.usage') ? 'bg-blue-600' : '' }}">
                        Usage Analytics
                    </a>
                </div>

                <div class="mt-8">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-3">System</h3>
                    <a href="{{ route('admin.audit-logs.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.audit-logs*') ? 'bg-blue-600' : '' }}">
                        Audit Logs
                    </a>
                </div>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-center font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 px-8 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" alt="Avatar" class="w-8 h-8 rounded-full">
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-8">
                @if($message = session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                        {{ $message }}
                    </div>
                @endif

                @if($message = session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                        {{ $message }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
