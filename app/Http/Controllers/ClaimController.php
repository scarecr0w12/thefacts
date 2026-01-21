<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Services\VerdictService;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index()
    {
        $search = request('q');
        
        $query = Claim::with(['creator', 'evidence']);
        
        if ($search) {
            $query->where('normalized_text', 'like', '%' . strtolower($search) . '%');
        }
        
        $claims = $query->orderBy('created_at', 'desc')->paginate(12);
        
        return view('claims.index', compact('claims', 'search'));
    }

    public function create()
    {
        return view('claims.create');
    }

    public function store(StoreClaimRequest $request)
    {
        $claim = Claim::create([
            'text' => $request->text,
            'normalized_text' => strtolower(trim($request->text)),
            'context_url' => $request->context_url,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('claims.show', $claim)->with('success', 'Claim created successfully!');
    }

    public function show(Claim $claim)
    {
        $claim->load(['creator', 'evidence.creator', 'evidence.votes']);
        return view('claims.show', compact('claim'));
    }

    // API endpoints
    public function apiIndex()
    {
        $claims = Claim::with(['creator', 'evidence'])->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($claims);
    }

    public function apiStore(StoreClaimRequest $request)
    {
        $claim = Claim::create([
            'text' => $request->text,
            'normalized_text' => strtolower(trim($request->text)),
            'context_url' => $request->context_url,
            'created_by' => auth()->id(),
        ]);

        return response()->json($claim, 201);
    }

    public function apiShow(Claim $claim)
    {
        $claim->load(['creator', 'evidence.creator', 'evidence.votes']);
        return response()->json($claim);
    }
}
