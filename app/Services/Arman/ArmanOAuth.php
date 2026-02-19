<?php

namespace App\Services\Arman;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ArmanOAuth
{
    private $token , $url;

    public function __construct()
    {
        $this->token = config('services.arman.oauth.token');
        $this->url = config('services.arman.oauth.api_endpoint');
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * @throws ConnectionException
     */
    public function sendRequest($callback , array $callback_data = []): ?string
    {
        $res = Http::acceptJson()
            ->baseUrl($this->url)
            ->get('/oauth/create',[
                'token' => $this->token,
                'callback' => $callback,
                'callback_data' => $callback_data
            ]);
        if (! $res->successful()) {
            throw new \Exception($res->body());
        }

        return $res->json()['verify_url'];
    }

    /**
     * @throws ConnectionException
     */
    public function verify(string $token): ?User
    {
        $res = Http::acceptJson()
            ->baseUrl($this->url)
            ->withToken($token)
            ->withHeader('X-API-Key',$this->token)
            ->get('/api/v1/auth/profile');
        if (! $res->successful()) {
            return null;
        }
        $user = $res->json('result');
        $model = User::query()->where('phone' , $user['phone']);
        if (! is_null($user['national_id'])) {
            $model->orWhere('national_id' , $user['national_id']);
        }
        $model = $model->first();
        if (! $model) {
            $model = User::query()
                ->firstOrCreate([
                    ['phone' , $user['phone']],
                ],[
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'national_id' => $user['national_id'],
                    'phone' => $user['phone'],
                    'arman_id' => $user['id'],
                    'avatar' => $user['avatar'],
                ]);
        } else {
            $model->fill([
                'phone' =>  $user['phone'],
                'national_id' =>  $user['national_id'],
                'name' =>  $user['name'],
                'avatar' =>  $user['avatar'],
            ]);
        }

        if (! $model->arman_id) {
            $model->fill(['arman_id' => $user['id']]);
        }
        $model->save();
        if (($user['role'] == 'super_admin' || $user['role'] == 'admin') && $model->wasRecentlyCreated) {
            $model->syncRoles(['admin']);
        }
        return $model;
    }
}
