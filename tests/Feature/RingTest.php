<?php

namespace Tests\Feature;

use App\Models\DashboardItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RingTest extends TestCase
{
    public function test_ring_can_be_store()
    {
        Storage::fake();
        $members = [
            'name' => fake()->name,
            'national_code' => "1234567898",
            'birthdate' => "1378/07/02",
            'postal_code' => "1234567898",
            'address' => fake()->address,
            'phone' => "09336332901",
            'image' => file_get_contents(public_path('testImg.jpeg'),'test.jpeg'),
            'father_name' => fake()->name
        ];
        $data = [
            'name' => fake()->name,
            'national_code' => "1234567898",
            'birthdate' => "1378/07/02",
            'postal_code' => "1234567898",
            'address' => fake()->address,
            'phone' => "09336332901",
            'level_of_education' => fake()->title,
            'field_of_study' => fake()->title,
            'job' => fake()->jobTitle,
            'sheba_number' => "IR12345",
            'skill_area' => [
                fake()->title,
                fake()->title,
            ],
            'functional_area' => [
                fake()->title,
                fake()->title,
            ],
            'image' => file_get_contents(public_path('testImg.jpeg'),'test.jpeg'),
            'members' => [
                $members , $members , $members , $members
            ]
        ];
        $user = User::query()->first();
        $this->actingAs($user)
            ->postJson('/api/v1/rings?item_id=2' , $data)
            ->assertSuccessful();
    }
}
