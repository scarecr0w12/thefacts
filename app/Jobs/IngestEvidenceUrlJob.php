<?php

namespace App\Jobs;

use App\Models\Evidence;
use App\Services\VerdictService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IngestEvidenceUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Evidence $evidence)
    {
    }

    public function handle(VerdictService $verdictService): void
    {
        try {
            $response = Http::timeout(15)
                ->userAgent('VeriCrowd/1.0 (+https://example.com)')
                ->get($this->evidence->url);

            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()}");
            }

            $html = $response->body();

            // Extract title
            $title = $this->extractTitle($html);
            
            // Extract publisher domain
            $domain = parse_url($this->evidence->url, PHP_URL_HOST);

            // Extract published date
            $publishedAt = $this->extractPublishedDate($html);

            $this->evidence->update([
                'title' => $title,
                'publisher_domain' => $domain,
                'published_at' => $publishedAt,
                'status' => 'READY',
                'error' => null,
            ]);

            // Recompute verdict
            $verdictService->computeVerdict($this->evidence->claim);

        } catch (\Exception $e) {
            Log::error("IngestEvidenceUrlJob failed for evidence {$this->evidence->id}: {$e->getMessage()}");
            
            $this->evidence->update([
                'status' => 'FAILED',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $html, $matches)) {
            return trim(html_entity_decode(strip_tags($matches[1])));
        }
        return null;
    }

    private function extractPublishedDate(string $html): ?\DateTime
    {
        // Try common meta tags
        $tags = [
            'article:published_time',
            'og:updated_time',
            'pubdate',
            'date',
        ];

        foreach ($tags as $tag) {
            if (preg_match('/<meta[^>]*property=["\']' . $tag . '["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                return $this->parseDate($matches[1]);
            }
            if (preg_match('/<meta[^>]*name=["\']' . $tag . '["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                return $this->parseDate($matches[1]);
            }
        }

        return null;
    }

    private function parseDate(string $dateString): ?\DateTime
    {
        try {
            return new \DateTime($dateString);
        } catch (\Exception $e) {
            Log::debug("Failed to parse date: {$dateString}");
            return null;
        }
    }
}
