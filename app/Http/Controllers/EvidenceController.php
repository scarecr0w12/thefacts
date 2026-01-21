<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceRequest;
use App\Jobs\IngestEvidenceUrlJob;
use App\Models\Claim;
use App\Models\Evidence;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    public function store(StoreEvidenceRequest $request, Claim $claim)
    {
        $evidence = Evidence::create([
            'claim_id' => $claim->id,
            'url' => $request->url,
            'stance' => $request->stance,
            'excerpt' => $request->excerpt,
            'status' => 'PENDING',
            'created_by' => auth()->id(),
        ]);

        // Dispatch async job to ingest URL
        IngestEvidenceUrlJob::dispatch($evidence);

        if (request()->expectsJson()) {
            return response()->json($evidence, 201);
        }

        return redirect()->route('claims.show', $claim)->with('success', 'Evidence submitted and processing...');
    }

    // API endpoint
    public function apiStore(StoreEvidenceRequest $request, Claim $claim)
    {
        $evidence = Evidence::create([
            'claim_id' => $claim->id,
            'url' => $request->url,
            'stance' => $request->stance,
            'excerpt' => $request->excerpt,
            'status' => 'PENDING',
            'created_by' => auth()->id(),
        ]);

        IngestEvidenceUrlJob::dispatch($evidence);

        return response()->json($evidence, 201);
    }
}
