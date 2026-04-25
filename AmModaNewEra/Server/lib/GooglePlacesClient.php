<?php
declare(strict_types=1);

namespace AmModa\Lib;

use RuntimeException;

final class GooglePlacesClient
{
    /** @var array<string,mixed> */
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array{rating: float, userRatingCount: int}
     */
    public function fetchPlaceDetails(): array
    {
        $url = rtrim($this->config['endpoint'], '/') . '/' . rawurlencode($this->config['place_id']);

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Nie udalo sie zainicjalizowac cURL.');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => (int) ($this->config['request_timeout_seconds'] ?? 8),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Goog-Api-Key: ' . $this->config['api_key'],
                'X-Goog-FieldMask: ' . $this->config['field_mask'],
            ],
        ]);

        $body = curl_exec($ch);
        $errNo = curl_errno($ch);
        $errMsg = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($errNo !== 0 || $body === false) {
            throw new RuntimeException('Blad cURL przy wywolaniu Google Places: ' . $errMsg);
        }

        if ($status !== 200) {
            throw new RuntimeException('Google Places zwrocil HTTP ' . $status . ': ' . (is_string($body) ? $body : ''));
        }

        $decoded = json_decode((string) $body, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Niepoprawny JSON w odpowiedzi Google Places.');
        }

        if (!isset($decoded['rating']) || !isset($decoded['userRatingCount'])) {
            throw new RuntimeException('Brak pol rating / userRatingCount w odpowiedzi Google Places.');
        }

        return [
            'rating'          => (float) $decoded['rating'],
            'userRatingCount' => (int) $decoded['userRatingCount'],
        ];
    }
}
