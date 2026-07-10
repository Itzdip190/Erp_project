<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\Notice;
use App\Models\Event;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeHolidayDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database (creates school, admin, classes, etc.)
        $this->seed(DatabaseSeeder::class);
    }

    public function test_multiple_holidays_and_notices_same_day(): void
    {
        // 1. Resolve School & School Admin
        $schoolAdmin = User::where('email', 'admin@yis.com')->firstOrFail();
        $schoolId = $schoolAdmin->school_id;

        $this->actingAs($schoolAdmin);

        // 2. Create 3 holidays on the same date (today)
        $today = now()->toDateString();
        Event::create([
            'school_id' => $schoolId,
            'title' => 'Holiday One Unique Test Title',
            'description' => 'First same-day holiday desc',
            'start_date' => $today,
            'end_date' => $today,
            'is_holiday' => true,
        ]);
        Event::create([
            'school_id' => $schoolId,
            'title' => 'Holiday Two Unique Test Title',
            'description' => 'Second same-day holiday desc',
            'start_date' => $today,
            'end_date' => $today,
            'is_holiday' => true,
        ]);
        Event::create([
            'school_id' => $schoolId,
            'title' => 'Holiday Three Unique Test Title',
            'description' => 'Third same-day holiday desc',
            'start_date' => $today,
            'end_date' => $today,
            'is_holiday' => true,
        ]);

        // 3. Create 1 holiday spanning multiple days (yesterday to tomorrow)
        $yesterday = now()->subDay()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        Event::create([
            'school_id' => $schoolId,
            'title' => 'Multi-Day Holiday Unique Test Title',
            'description' => 'Multi-day holiday description',
            'start_date' => $yesterday,
            'end_date' => $tomorrow,
            'is_holiday' => true,
        ]);

        // 4. Create 2 notices on the same date (today)
        Notice::create([
            'school_id' => $schoolId,
            'title' => 'Notice One Unique Test Title',
            'content' => 'First same-day notice content',
            'target_audience' => 'all',
        ]);
        Notice::create([
            'school_id' => $schoolId,
            'title' => 'Notice Two Unique Test Title',
            'content' => 'Second same-day notice content',
            'target_audience' => 'staff',
        ]);

        // 5. Fetch notice board page and assert all 6 items are visible
        $response = $this->get('/school/communication/notice');
        $response->assertStatus(200);
        $response->assertSee('Holiday One Unique Test Title');
        $response->assertSee('Holiday Two Unique Test Title');
        $response->assertSee('Holiday Three Unique Test Title');
        $response->assertSee('Multi-Day Holiday Unique Test Title');
        $response->assertSee('Notice One Unique Test Title');
        $response->assertSee('Notice Two Unique Test Title');

        // 6. Fetch school dashboard and assert all 6 items are present in the bell dropdown topbar
        $dashboardResponse = $this->get('/school/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Holiday One Unique Test Title');
        $dashboardResponse->assertSee('Holiday Two Unique Test Title');
        $dashboardResponse->assertSee('Holiday Three Unique Test Title');
        $dashboardResponse->assertSee('Multi-Day Holiday Unique Test Title');
        $dashboardResponse->assertSee('Notice One Unique Test Title');
        $dashboardResponse->assertSee('Notice Two Unique Test Title');
    }
}
