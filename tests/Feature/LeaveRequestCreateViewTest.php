<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestCreateViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_sees_prefilled_identity_fields_on_leave_request_form(): void
    {
        $employee = User::factory()->create([
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        $response = $this->actingAs($employee)->get(route('leave-requests.create'));

        $response->assertOk();
        $response->assertSee('value="Jean"', false);
        $response->assertSee('value="Dupont"', false);
        $response->assertSee('value="jean@example.com"', false);
        $response->assertDontSee('selected_employee_id', false);
    }

    public function test_admin_can_select_an_employee_on_leave_request_form(): void
    {
        $admin = User::factory()->create([
            'status' => User::STATUS_ADMIN,
        ]);

        $employee = User::factory()->create([
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        User::factory()->create([
            'status' => User::STATUS_PAYROLL_MANAGER,
        ]);

        $response = $this->actingAs($admin)->get(route('leave-requests.create'));

        $response->assertOk();
        $response->assertSee('selected_employee_id', false);
        $response->assertSee('Alice Martin', false);
        $response->assertSee('data-email="alice@example.com"', false);
        $response->assertDontSee('payroll_manager', false);
        $response->assertSee('data-first-name="Alice"', false);
        $response->assertSee('data-last-name="Martin"', false);
    }
}
