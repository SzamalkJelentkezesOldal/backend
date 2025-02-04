<?php

namespace Tests\Feature;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_users(): void
    {
        $response = $this->withoutMiddleware()->get('/api/users');

        $response->assertStatus(200);
    }

    public function test_users_auth() : void {
            //$this->withoutExceptionHandling(); 
            // create rögzíti az adatbázisban a felh-t
            $admin = User::factory()->make([
                'role' => 1,
            ]);
            $response = $this->actingAs($admin)->get('/api/users/'.$admin->id);
            $response->assertStatus(200);
        }
}
