<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VeriCrowd - Crowdsourced Fact-Checking')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('claims.index') }}" class="text-2xl font-bold text-blue-600">
                        VeriCrowd
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @if (auth()->check())
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:text-blue-800">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Register</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($message = Session::get('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                {{ $message }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
