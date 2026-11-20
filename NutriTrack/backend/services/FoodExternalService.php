
<?php

class FoodExternalService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';

        $this->apiKey = $config['external_api']['usda_api_key'];
        $this->baseUrl = rtrim(
            $config['external_api']['usda_base_url'],
            '/'
        );

        if (
            empty($this->apiKey) ||
            $this->apiKey === 'YOUR_USDA_API_KEY_HERE'
        ) {
            throw new RuntimeException('USDA API key is not configured.');
        }
    }

    public function searchFoods(string $query, int $pageSize = 10): array
    {
        $query = trim($query);

        if ($query === '') {
            throw new InvalidArgumentException('Search query is required.');
        }

        if ($pageSize < 1 || $pageSize > 25) {
            throw new InvalidArgumentException(
                'Page size must be between 1 and 25.'
            );
        }

        $url = $this->baseUrl . '/foods/search?' . http_build_query([
            'api_key' => $this->apiKey,
            'query' => $query,
            'pageSize' => $pageSize
        ]);

        $response = $this->sendRequest($url);

        return array_map(function ($food) {
            return [
                'fdc_id' => $food['fdcId'] ?? null,
                'name' => $food['description'] ?? 'Unknown food',
                'data_type' => $food['dataType'] ?? null,
                'calories' => $this->getNutrient($food, 1008),
                'protein' => $this->getNutrient($food, 1003),
                'carbohydrates' => $this->getNutrient($food, 1005),
                'fat' => $this->getNutrient($food, 1004)
            ];
        }, $response['foods'] ?? []);
    }

    private function getNutrient(array $food, int $nutrientId): ?float
    {
        foreach ($food['foodNutrients'] ?? [] as $nutrient) {
            $id = $nutrient['nutrientId']
                ?? $nutrient['nutrient']['id']
                ?? null;

            if ((int) $id === $nutrientId) {
                $amount = $nutrient['value']
                    ?? $nutrient['amount']
                    ?? null;

                return $amount !== null ? (float) $amount : null;
            }
        }

        return null;
    }

    private function sendRequest(string $url): array
    {
        $curl = curl_init($url);

        curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_CAINFO => __DIR__ . '/../config/certificates/ca-bundle.crt',
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json'
    ]
]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);

            throw new RuntimeException(
                'USDA API connection failed: ' . $error
            );
        }

        curl_close($curl);

        if ($httpCode === 429) {
            throw new RuntimeException('USDA API rate limit exceeded.');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException(
                'USDA API returned HTTP status ' . $httpCode
            );
        }
        $data = json_decode($response, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response from USDA API.');
        }

        return $data;
    }
}
