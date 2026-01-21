<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index()
    {
        $claims = Claim::with(['creator', 'evidence'])
            ->latest()
            ->paginate(20);

        return view('admin.claims.index', ['claims' => $claims]);
    }

    public function show(Claim $claim)
    {
        $claim->load(['creator', 'evidence' => function ($query) {
            $query->with(['creator', 'votes']);
        }]);

        return view('admin.claims.show', ['claim' => $claim]);
    }

    public function update(Request $request, Claim $claim)
    {
        $request->validate([
            'text' => 'required|string',
            'verdict' => 'nullable|in:TRUE,FALSE,MIXED,UNVERIFIED',
            'confidence' => 'nullable|integer|min:0|max:100',
        ]);

        $before = $claim->toArray();
        
        $claim->update($request->only(['text', 'verdict', 'confidence']));
        
        AuditLog::log('update', 'Claim', $claim->id, $before, $claim->fresh()->toArray());

        return redirect()->route('admin.claims.show', $claim)
            ->with('success', 'Claim updated successfully.');
    }

    public function destroy(Claim $claim)
    {
        AuditLog::log('delete', 'Claim', $claim->id, $claim->toArray());
        
        $claim->delete();

        return redirect()->route('admin.claims.index')
            ->with('success', 'Claim deleted successfully.');
    }
}
