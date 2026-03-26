<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_cancel_own_pending_leave_request(): void
    {
        $employee = User::factory()->create([
            'email' => 'jean@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        $leaveRequest = LeaveRequest::create([
            'employee_name' => 'Jean Dupont',
            'employee_email' => 'jean@example.com',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-12',
            'reason' => 'Congés payés',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employee)
            ->delete(route('leave-requests.cancel', $leaveRequest));

        $response->assertRedirect(route('leave-requests.index'));
        $response->assertSessionHas('status', 'Votre demande de congé a bien été annulée.');
        $this->assertDatabaseMissing('leave_requests', ['id' => $leaveRequest->id]);
    }

    public function test_employee_cannot_cancel_request_of_another_employee(): void
    {
        $employee = User::factory()->create([
            'email' => 'jean@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        $leaveRequest = LeaveRequest::create([
            'employee_name' => 'Alice Martin',
            'employee_email' => 'alice@example.com',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-12',
            'reason' => 'Congés payés',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employee)
            ->delete(route('leave-requests.cancel', $leaveRequest));

        $response->assertForbidden();
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveRequest->id]);
    }

    public function test_employee_cannot_cancel_non_pending_leave_request(): void
    {
        $employee = User::factory()->create([
            'email' => 'jean@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        $leaveRequest = LeaveRequest::create([
            'employee_name' => 'Jean Dupont',
            'employee_email' => 'jean@example.com',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-12',
            'reason' => 'Congés payés',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($employee)
            ->delete(route('leave-requests.cancel', $leaveRequest));

        $response->assertRedirect(route('leave-requests.index'));
        $response->assertSessionHas('status', 'Seules les demandes en attente peuvent être annulées.');
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveRequest->id]);
    }
}
