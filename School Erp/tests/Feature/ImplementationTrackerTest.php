<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\School;
use App\Models\AcademicSession;
use App\Models\ImplementationTracker\DataImplementation;
use App\Models\ImplementationTracker\TemplateImplementation;
use App\Models\ImplementationTracker\Integration;
use App\Models\ImplementationTracker\Training;
use App\Models\ImplementationTracker\ImplActivityLog;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImplementationTrackerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the entire database including implementation seeders
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test GET /school/implementation-tracker loads successfully.
     */
    public function test_tracker_page_loads(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/implementation-tracker');

        $response->assertStatus(200);
        $response->assertViewHas('dataImpl');
        $response->assertViewHas('tempImpl');
        $response->assertViewHas('integrations');
        $response->assertViewHas('trainings');
        
        // Assert some standard seeded data exists for school
        $this->assertGreaterThan(0, DataImplementation::where('school_id', $schoolAdmin->school_id)->count());
        $this->assertGreaterThan(0, TemplateImplementation::where('school_id', $schoolAdmin->school_id)->count());
        $this->assertGreaterThan(0, Integration::where('school_id', $schoolAdmin->school_id)->count());
        $this->assertGreaterThan(0, Training::where('school_id', $schoolAdmin->school_id)->count());
    }

    /**
     * Test PUT /school/implementation-tracker/update/data tab updates values and creates logs.
     */
    public function test_tracker_update_data_tab(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $row = DataImplementation::where('school_id', $schoolAdmin->school_id)->first();
        
        $postData = [
            'rows' => [
                $row->id => [
                    'data_received_date' => '20/06/2026, 10:00',
                    'data_implemented_on' => '21/06/2026, 12:30',
                    'tat' => '', // should auto-calculate to "1 days"
                    'owner_school_side' => 'Admin Staff',
                    'confirmation_school_side' => 'Confirmed',
                    'comment' => 'Seeding completed'
                ]
            ]
        ];

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post("/school/implementation-tracker/update/data", array_merge($postData, ['_method' => 'PUT']));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert row updated
        $updatedRow = $row->refresh();
        $this->assertEquals('1 days', $updatedRow->tat);
        $this->assertEquals('Admin Staff', $updatedRow->owner_school_side);
        $this->assertEquals('Confirmed', $updatedRow->confirmation_school_side);
        // Status should auto-map to Completed when confirmation is Confirmed
        $this->assertEquals('Completed', $updatedRow->status);

        // Assert audit trail log created
        $logs = ImplActivityLog::where('school_id', $schoolAdmin->school_id)->get();
        $this->assertGreaterThan(0, $logs->count());
        $this->assertContains('Tat', $logs->pluck('field_changed')->toArray());
        $this->assertContains('Confirmation School Side', $logs->pluck('field_changed')->toArray());
    }

    /**
     * Test GET /school/implementation-tracker/logs returns json logs.
     */
    public function test_tracker_logs_endpoint(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // Let's perform an update first to generate a log entry
        $row = Integration::where('school_id', $schoolAdmin->school_id)->first();
        $postData = [
            'rows' => [
                $row->id => [
                    'company' => 'New Corp',
                    'serial_number' => '12345',
                    'vendor_contact_details' => '9999999999',
                    'api_received_on' => '21/06/2026, 12:00',
                    'implemented_on' => '21/06/2026, 14:00',
                    'tat' => '2 hours',
                    'owner_school_side' => 'Admin Staff',
                    'confirmation_school_side' => 'Confirmed',
                    'comment' => 'Integration done'
                ]
            ]
        ];

        $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post("/school/implementation-tracker/update/integrations", array_merge($postData, ['_method' => 'PUT']));

        // Get logs
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/implementation-tracker/logs');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertEquals('Integrations', $data[0]['tab_name']);
        $this->assertEquals($row->integration_name, $data[0]['row_reference']);
    }
}
