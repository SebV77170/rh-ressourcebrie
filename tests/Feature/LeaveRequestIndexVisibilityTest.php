<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestIndexVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_only_sees_own_leave_requests_on_dashboard(): void
    {
        $employee = User::factory()->create([
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        LeaveRequest::create([
            'employee_name' => 'Jean Dupont',
            'employee_email' => 'jean@example.com',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-12',
            'reason' => 'Congés payés',
            'status' => 'pending',
        ]);

        LeaveRequest::create([
            'employee_name' => 'Alice Martin',
            'employee_email' => 'alice@example.com',
            'start_date' => '2026-03-15',
            'end_date' => '2026-03-18',
            'reason' => 'Vacances',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($employee)->get(route('leave-requests.index'));

        $response->assertOk();
        $response->assertSee('Jean Dupont');
        $response->assertSee('jean@example.com');
        $response->assertDontSee('Alice Martin');
        $response->assertDontSee('alice@example.com');
        $response->assertSee('Consultation uniquement');
    }

    public function test_admin_still_sees_all_leave_requests_on_dashboard(): void
    {
        $admin = User::factory()->create([
            'status' => User::STATUS_ADMIN,
        ]);

        LeaveRequest::create([
            'employee_name' => 'Jean Dupont',
            'employee_email' => 'jean@example.com',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-12',
            'reason' => 'Congés payés',
            'status' => 'pending',
        ]);

        LeaveRequest::create([
            'employee_name' => 'Alice Martin',
            'employee_email' => 'alice@example.com',
            'start_date' => '2026-03-15',
            'end_date' => '2026-03-18',
            'reason' => 'Vacances',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get(route('leave-requests.index'));

        $response->assertOk();
        $response->assertSee('Jean Dupont');
        $response->assertSee('Alice Martin');
    }
}
