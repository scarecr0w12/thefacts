@extends('layout')

@section('title', 'Login - VeriCrowd')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg border border-gray-200">
    <h2 class="text-2xl font-bold mb-6">Login</h2>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-medium">
            Login
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
        Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
    </p>
</div>
@endsection
