<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evidence;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    public function index()
    {
        $evidence = Evidence::with(['claim', 'creator'])
            ->latest()
            ->paginate(20);

        return view('admin.evidence.index', ['evidence' => $evidence]);
    }

    public function show(Evidence $evidence)
    {
        $evidence->load(['claim', 'creator', 'votes' => function ($query) {
            $query->with('user');
        }]);

        return view('admin.evidence.show', ['evidence' => $evidence]);
    }

    public function update(Request $request, Evidence $evidence)
    {
        $request->validate([
            'status' => 'required|in:PENDING,READY,FAILED',
            'stance' => 'required|in:SUPPORTS,REFUTES,CONTEXT',
        ]);

        $before = $evidence->toArray();
        
        $evidence->update($request->only(['status', 'stance']));
        
        AuditLog::log('update', 'Evidence', $evidence->id, $before, $evidence->fresh()->toArray());

        return redirect()->route('admin.evidence.show', $evidence)
            ->with('success', 'Evidence updated successfully.');
    }

    public function destroy(Evidence $evidence)
    {
        AuditLog::log('delete', 'Evidence', $evidence->id, $evidence->toArray());
        
        $evidence->delete();

        return redirect()->route('admin.evidence.index')
            ->with('success', 'Evidence deleted successfully.');
    }
}
