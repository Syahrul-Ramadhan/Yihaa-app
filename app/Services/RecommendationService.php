<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    protected $aiServiceUrl;

    public function __construct()
    {
        // URL layanan AI (FastAPI)
        // Jika running di Docker dan Laravel juga di Docker, gunakan nama service (e.g., http://yihaa-ai:8000)
        // Jika Laravel running di host (XAMPP/Local), gunakan http://localhost:8000
        $this->aiServiceUrl = env('AI_SERVICE_URL', 'http://localhost:8000');
    }

    /**
     * Get recommended post IDs for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecommendations(int $userId, int $limit = 10): array
    {
        try {
            $response = Http::timeout(5)->post("{$this->aiServiceUrl}/recommend", [
                'user_id' => $userId,
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommendations = $data['recommendations'] ?? [];

                // Extract post_ids from the recommendation list
                // Format response AI: [{"post_id": 123, "similarity": 0.9}, ...]
                return array_column($recommendations, 'post_id');
            } else {
                Log::warning("AI Service Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("AI Service Connection Failed: " . $e->getMessage());
        }

        return [];
    }
}
