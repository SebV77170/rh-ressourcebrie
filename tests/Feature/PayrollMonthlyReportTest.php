<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollMonthlyReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_report_limits_an_overlapping_period_to_the_selected_month(): void
    {
        $payrollManager = User::factory()->create([
            'status' => User::STATUS_PAYROLL_MANAGER,
        ]);

        LeaveRequest::create([
            'employee_name' => 'Jean Dupont',
            'employee_email' => 'jean@example.com',
            'start_date' => '2026-03-27',
            'end_date' => '2026-04-04',
            'reason' => 'Congés payés',
            'status' => 'approved',
        ]);

        $marchResponse = $this->actingAs($payrollManager)->get(route('leave-requests.index', [
            'month' => 3,
            'year' => 2026,
        ]));

        $marchResponse->assertOk();
        $marchResponse->assertSee('27/03 au 31/03/2026', false);
        $marchResponse->assertDontSee('01/04 au 04/04/2026', false);

        $aprilResponse = $this->actingAs($payrollManager)->get(route('leave-requests.index', [
            'month' => 4,
            'year' => 2026,
        ]));

        $aprilResponse->assertOk();
        $aprilResponse->assertSee('01/04 au 04/04/2026', false);
        $aprilResponse->assertDontSee('27/03 au 31/03/2026', false);
    }
}
