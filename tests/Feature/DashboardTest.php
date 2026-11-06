<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_based_on_their_role(): void
    {
        $roles = [
            'admin' => '/admin',
            'driver' => '/driver',
            'passenger' => '/tracking',
        ];

        foreach ($roles as $role => $expectedPath) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $response = $this->actingAs($user)->get(route('dashboard'));

            $response->assertRedirect($expectedPath);
        }
    }
}