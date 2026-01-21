<?php

namespace App\Services;

use App\Models\Claim;

class VerdictService
{
    public function computeVerdict(Claim $claim): void
    {
        $readyEvidence = $claim->evidence()->where('status', 'READY')->get();

        if ($readyEvidence->count() < 2) {
            $claim->update([
                'verdict' => 'UNVERIFIED',
                'confidence' => 0,
            ]);
            return;
        }

        $supportsVotes = 0;
        $refutesVotes = 0;

        foreach ($readyEvidence as $evidence) {
            $voteSum = $evidence->votes()->sum('value');
            
            if ($evidence->stance === 'SUPPORTS') {
                $supportsVotes += $voteSum;
            } elseif ($evidence->stance === 'REFUTES') {
                $refutesVotes += $voteSum;
            }
        }

        $score = $supportsVotes - $refutesVotes;

        if ($score >= 3) {
            $verdict = 'TRUE';
            $confidence = min(90, 50 + (10 * $score));
        } elseif ($score <= -3) {
            $verdict = 'FALSE';
            $confidence = min(90, 50 + (10 * abs($score)));
        } else {
            $verdict = 'MIXED';
            $confidence = 50;
        }

        $claim->update([
            'verdict' => $verdict,
            'confidence' => $confidence,
        ]);
    }
}
