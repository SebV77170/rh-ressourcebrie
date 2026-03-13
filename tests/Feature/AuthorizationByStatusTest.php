<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationByStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_manager_cannot_access_leave_creation_form(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_PAYROLL_MANAGER,
        ]);

        $response = $this->actingAs($user)->get('/conges/demande');

        $response->assertForbidden();
    }

    public function test_admin_can_access_leave_validation_route(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_ADMIN,
        ]);

        $response = $this->actingAs($user)->get('/conges');

        $response->assertOk();
    }
}
