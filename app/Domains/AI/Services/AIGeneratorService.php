<?php

namespace App\Domains\AI\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Client as OpenAIClient;

class AIGeneratorService
{
    public function __construct(
        private OpenAIClient $openai
    ) {}

    public function generateProductDescription(Client $client, array $productData): ?string
    {
        // Проверка доступности ИИ для тарифа
        if (! $client->plan->ai_enabled) {
            Log::warning('AI generation not allowed for current plan', [
                'client_id' => $client->id,
                'plan' => $client->plan->name,
            ]);

            return null;
        }

        $cacheKey = 'ai_product_description_'.md5(json_encode($productData));

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($productData) {
            try {
                $prompt = $this->buildComprehensivePrompt($productData);

                $response = $this->openai->chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional product description writer.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 250,
                ]);

                $choices = $response->choices ?? [];
                $content = $choices[0]->message->content ?? null;

                return $content;
            } catch (\Exception $e) {
                Log::error('AI Description Generation Failed', [
                    'error' => $e->getMessage(),
                    'product_data' => $productData,
                ]);

                return null;
            }
        });
    }

    public function generateShopGreeting(Client $client, array $shopData): ?string
    {
        if (! $client->plan->ai_enabled) {
            return null;
        }

        $cacheKey = 'ai_shop_greeting_'.md5(json_encode($shopData));

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($shopData) {
            try {
                $prompt = $this->buildShopGreetingPrompt($shopData);

                $response = $this->openai->chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a friendly and professional shop assistant.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 150,
                ]);

                $choices = $response->choices ?? [];
                $content = $choices[0]->message->content ?? null;

                return $content;
            } catch (\Exception $e) {
                Log::error('AI Shop Greeting Generation Failed', [
                    'error' => $e->getMessage(),
                    'shop_data' => $shopData,
                ]);

                return null;
            }
        });
    }

    private function buildComprehensivePrompt(array $productData): string
    {
        return "Create a compelling, SEO-friendly product description for a product with these details:\n".
               "- Product Name: {$productData['name']}\n".
               '- Key Details: '.json_encode($productData['details'] ?? [])."\n".
               'Write in a professional tone, highlight key benefits, and include a call to action.';
    }

    private function buildShopGreetingPrompt(array $shopData): string
    {
        $category = $shopData['category'] ?? 'General';

        return "Create a warm and inviting greeting message for an online shop:\n".
               "- Shop Name: {$shopData['name']}\n".
               "- Shop Type/Category: {$category}\n".
               'Create a welcoming message that reflects the shop\'s personality and encourages customers to explore.';
    }
}
