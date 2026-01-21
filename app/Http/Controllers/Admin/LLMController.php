<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LLMConfig;
use App\Models\LLMUsage;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LLMController extends Controller
{
    public function config()
    {
        $config = LLMConfig::first();

        return view('admin.llm.config', ['config' => $config]);
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'enabled' => 'boolean',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|min:0|max:2',
            'system_prompt' => 'nullable|string',
            'cost_per_1k_tokens' => 'required|numeric|min:0',
        ]);

        $config = LLMConfig::first() ?? new LLMConfig();
        $before = $config->toArray();

        $config->fill($request->only([
            'provider', 'model', 'enabled', 'max_tokens', 'temperature', 'system_prompt', 'cost_per_1k_tokens'
        ]));

        if ($request->filled('api_key')) {
            $config->api_key = $request->api_key;
        }

        $config->save();

        AuditLog::log('update', 'LLMConfig', $config->id, $before, $config->fresh()->toArray());

        return redirect()->route('admin.llm.config')
            ->with('success', 'LLM configuration updated successfully.');
    }

    public function usage()
    {
        $period = request('period', '7'); // days
        $startDate = Carbon::now()->subDays($period);

        $usages = LLMUsage::where('created_at', '>=', $startDate)
            ->with('user')
            ->latest()
            ->paginate(20);

        $dailyStats = LLMUsage::selectRaw('
            DATE(created_at) as date,
            COUNT(*) as count,
            SUM(input_tokens) as input_tokens,
            SUM(output_tokens) as output_tokens,
            SUM(cost) as cost
        ')
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $actionStats = LLMUsage::selectRaw('
            action,
            COUNT(*) as count,
            SUM(cost) as total_cost,
            AVG(response_time_ms) as avg_response_time
        ')
        ->where('created_at', '>=', $startDate)
        ->groupBy('action')
        ->get();

        $totalCost = LLMUsage::where('created_at', '>=', $startDate)->sum('cost');
        $totalTokens = LLMUsage::where('created_at', '>=', $startDate)
            ->selectRaw('SUM(input_tokens + output_tokens) as total')
            ->value('total') ?? 0;

        return view('admin.llm.usage', [
            'usages' => $usages,
            'dailyStats' => $dailyStats,
            'actionStats' => $actionStats,
            'totalCost' => $totalCost,
            'totalTokens' => $totalTokens,
            'period' => $period,
        ]);
    }
}
