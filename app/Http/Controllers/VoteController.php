<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoteRequest;
use App\Models\Evidence;
use App\Services\VerdictService;

class VoteController extends Controller
{
    public function store(StoreVoteRequest $request, Evidence $evidence)
    {
        $user = auth()->user();

        // Check or update vote
        $vote = $evidence->votes()->where('user_id', $user->id)->first();

        if ($vote) {
            $vote->update(['value' => $request->value]);
        } else {
            $evidence->votes()->create([
                'user_id' => $user->id,
                'value' => $request->value,
            ]);
        }

        // Recompute verdict
        app(VerdictService::class)->computeVerdict($evidence->claim);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'vote' => $vote ?? $evidence->votes()->where('user_id', $user->id)->first()]);
        }

        return redirect()->back()->with('success', 'Vote recorded!');
    }

    // API endpoint
    public function apiStore(StoreVoteRequest $request, Evidence $evidence)
    {
        $user = auth()->user();

        $vote = $evidence->votes()->where('user_id', $user->id)->first();

        if ($vote) {
            $vote->update(['value' => $request->value]);
        } else {
            $vote = $evidence->votes()->create([
                'user_id' => $user->id,
                'value' => $request->value,
            ]);
        }

        app(VerdictService::class)->computeVerdict($evidence->claim);

        return response()->json(['success' => true, 'vote' => $vote]);
    }
}
