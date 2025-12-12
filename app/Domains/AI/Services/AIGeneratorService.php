<?php

namespace App\Domains\AI\Services;

use App\Models\Client;
use OpenAI\Client as OpenAIClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class AIGeneratorService
{
    /**
     * @var \OpenAI\Client
     */
    private $openai;

    /**
     * @param \OpenAI\Client $openai
     */
    public function __construct($openai)
    {
        $this->openai = $openai;
    }

    public function generateProductDescription(Client $client, array $productData): ?string
    {
        $cacheKey = 'product_description_' . md5($productData['name'] ?? json_encode($productData));

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($productData) {
            try {
                $response = $this->openai->completions()->create([
                    'model' => 'gpt-3.5-turbo',
                    'prompt' => $this->buildPrompt($productData),
                    'max_tokens' => 150
                ]);

                return $response['choices'][0]['text'] ?? null;
            } catch (\Exception $e) {
                Log::error('AI Description Generation Failed', [
                    'error' => $e->getMessage(),
                    'product_data' => $productData
                ]);

                return null;
            }
        });
    }

    public function buildPrompt(array $productData): string
    {
        return "Create a compelling product description for a product named '{$productData['name']}' " .
               "with the following details: " . 
               json_encode($productData);
    }
}