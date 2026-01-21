<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriCrowd Installation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-blue-600 mb-2">VeriCrowd</h1>
                <p class="text-gray-600">Installation Wizard</p>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold mb-2">Installation failed:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('installer.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Application Settings -->
                <div class="border-t pt-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Application Settings</h2>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                            <input type="text" name="app_name" value="VeriCrowd" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                            <input type="url" name="app_url" placeholder="https://example.com" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Your VeriCrowd URL (e.g., https://yoursite.com)</p>
                        </div>
                    </div>
                </div>

                <!-- Database Configuration -->
                <div class="border-t pt-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Database Configuration</h2>
                    <p class="text-sm text-gray-600 mb-4">Configure your MariaDB database connection</p>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Host</label>
                            <input type="text" name="db_host" value="localhost" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">e.g., localhost or 127.0.0.1</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Port</label>
                            <input type="number" name="db_port" value="3306" required min="1" max="65535"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Default: 3306</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                            <input type="text" name="db_database" value="vericrown" required
                                pattern="[a-zA-Z0-9_]+"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Letters, numbers, underscore only</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Username</label>
                            <input type="text" name="db_username" placeholder="vericrown" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Password</label>
                            <input type="password" name="db_password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Leave empty if no password required</p>
                        </div>
                    </div>
                </div>

                <!-- Admin User -->
                <div class="border-t pt-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Administrator Account</h2>
                    <p class="text-sm text-gray-600 mb-4">Create your initial admin user account</p>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Name</label>
                            <input type="text" name="admin_name" placeholder="Administrator" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                            <input type="email" name="admin_email" placeholder="admin@example.com" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" name="admin_password" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="admin_password_confirmation" required minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="border-t pt-8 flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition">
                        Complete Installation
                    </button>
                </div>
            </form>

            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded">
                <p class="text-xs text-blue-800">
                    <strong>Important:</strong> Keep your credentials secure. After installation, you can log in with your admin email and password.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
