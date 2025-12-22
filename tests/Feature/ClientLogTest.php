<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ClientLogTest extends TestCase
{
//    use DatabaseTruncation;
    /**
     * A basic feature test example.
     */
    public function test_log_should_be_store_without_user(): void
    {
        $data = [
            'context' => 'test',
            'client_version' => '0.0.1',
            'platform' => 'web'
        ];
        $res = $this->postJson('/api/v1/client-log',$data)
            ->assertSuccessful();

        $res = $res->json('data');

        $this->assertDatabaseHas('client_logs' , [
            ... $data,
            'id' => $res['id']
        ]);
    }
    public function test_log_should_be_store_with_user(): void
    {
        $data = [
            'context' => 'test',
            'client_version' => '0.0.1',
            'platform' => 'web'
        ];
        $user = Auth::loginUsingId(24);
        $res = $this->actingAs($user)->postJson('/api/v1/client-log',$data)
            ->assertSuccessful();

        $res = $res->json('data');

        $this->assertDatabaseHas('client_logs' , [
            ... $data,
            'id' => $res['id'],
            'user_id' => $user->id
        ]);
    }
}
