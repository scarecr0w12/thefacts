@extends('admin.layout')

@section('title', 'LLM Configuration')
@section('page-title', 'LLM Configuration')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Provider Settings</h3>

            <form action="{{ route('admin.llm.update-config') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                            <select name="provider" id="provider" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="openai" {{ $config?->provider === 'openai' ? 'selected' : '' }}>OpenAI</option>
                                <option value="anthropic" {{ $config?->provider === 'anthropic' ? 'selected' : '' }}>Anthropic</option>
                                <option value="deepseek" {{ $config?->provider === 'deepseek' ? 'selected' : '' }}>DeepSeek</option>
                                <option value="custom" {{ $config?->provider === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" name="model" id="model" value="{{ $config?->model ?? 'gpt-4-turbo' }}" placeholder="gpt-4-turbo" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        <input type="password" name="api_key" id="api_key" placeholder="Leave empty to keep current key" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm">
                        <p class="text-xs text-gray-500 mt-1">🔒 Never displayed, only updated when you enter a new key</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="max_tokens" class="block text-sm font-medium text-gray-700 mb-1">Max Tokens</label>
                            <input type="number" name="max_tokens" id="max_tokens" value="{{ $config?->max_tokens ?? 1000 }}" min="1" max="10000" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">Temperature</label>
                            <input type="number" name="temperature" id="temperature" value="{{ $config?->temperature ?? 0.7 }}" min="0" max="2" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">0 = deterministic, 2 = creative</p>
                        </div>
                    </div>

                    <div>
                        <label for="cost_per_1k_tokens" class="block text-sm font-medium text-gray-700 mb-1">Cost per 1K Tokens ($)</label>
                        <input type="number" name="cost_per_1k_tokens" id="cost_per_1k_tokens" value="{{ $config?->cost_per_1k_tokens ?? 0.01 }}" min="0" step="0.0001" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label for="system_prompt" class="block text-sm font-medium text-gray-700 mb-1">System Prompt</label>
                        <textarea name="system_prompt" id="system_prompt" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm">{{ $config?->system_prompt }}</textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="enabled" value="1" {{ $config?->enabled ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm font-medium text-gray-700">Enable LLM Integration</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-gray-900 mb-4">Current Status</h4>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Status</dt>
                    <dd class="flex items-center gap-2 mt-1">
                        <span class="{{ $config?->enabled ? 'inline-block w-3 h-3 bg-green-500 rounded-full' : 'inline-block w-3 h-3 bg-red-500 rounded-full' }}"></span>
                        <span class="font-semibold text-gray-900">{{ $config?->enabled ? 'Enabled' : 'Disabled' }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Provider</dt>
                    <dd class="font-semibold text-gray-900 mt-1">{{ $config?->provider ?? 'Not configured' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-600 uppercase font-semibold">Model</dt>
                    <dd class="font-semibold text-gray-900 mt-1">{{ $config?->model ?? 'Not configured' }}</dd>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <dt class="text-xs text-gray-600 uppercase font-semibold">API Key</dt>
                    <dd class="text-sm text-gray-900 mt-1">{{ $config && $config->api_key ? '✓ Configured' : '✗ Not configured' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
