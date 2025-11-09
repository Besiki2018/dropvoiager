<?php

namespace Modules\Car\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDirectionsService
{
    protected string $endpoint = 'https://maps.googleapis.com/maps/api/directions/json';

    public function __construct(protected ?string $apiKey = null)
    {
        $this->apiKey = $this->apiKey ?: config('services.google_maps.key');
    }

    public function distanceInKm(?array $origin, ?array $destination): ?float
    {
        if (!$this->apiKey || !$this->isPointValid($origin) || !$this->isPointValid($destination)) {
            return null;
        }

        $response = Http::timeout(10)->get($this->endpoint, [
            'origin' => $origin['lat'] . ',' . $origin['lng'],
            'destination' => $destination['lat'] . ',' . $destination['lng'],
            'key' => $this->apiKey,
            'units' => 'metric',
        ]);

        if (!$response->successful()) {
            Log::warning('GoogleDirectionsService request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $payload = $response->json();
        if (($payload['status'] ?? '') !== 'OK') {
            Log::warning('GoogleDirectionsService API error', [
                'status' => $payload['status'] ?? null,
                'error_message' => $payload['error_message'] ?? null,
            ]);
            return null;
        }

        $meters = data_get($payload, 'routes.0.legs.0.distance.value');
        if (!$meters) {
            return null;
        }

        return round($meters / 1000, 2);
    }

    protected function isPointValid(?array $point): bool
    {
        if (!is_array($point)) {
            return false;
        }

        return isset($point['lat'], $point['lng']) && is_numeric($point['lat']) && is_numeric($point['lng']);
    }
}
