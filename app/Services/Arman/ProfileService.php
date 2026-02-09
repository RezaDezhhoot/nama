<?php

namespace App\Services\Arman;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class ProfileService
{
    private $config;
    public function __construct()
    {
        $this->config = config('services.arman.profile');
    }

    public static function new(): static
    {
        return new static();
    }

    private function newReq(): PendingRequest
    {
        return Http::acceptJson()
            ->baseUrl($this->config['base_url']);
    }

    /**
     * @throws ConnectionException
     */
    public function submission($nationalID)
    {
        $res = $this->newReq()
            ->get('/api/v1/form-submissions/'.$nationalID);

        if (! $res->successful()) {
            throw new \Exception($res->body());
        }

        return $res->json();
    }
}
