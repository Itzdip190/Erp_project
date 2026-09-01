<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\Visitor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisitorRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->school = School::first();
        $this->admin = User::where('school_id', $this->school->id)->where('email', 'admin@yis.com')->first();
    }

    /**
     * Test 1: Visitor Registration page loads successfully.
     */
    public function test_visitor_registration_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('school.front-desk.visitor-registration'));

        $response->assertStatus(200);
        $response->assertSee('Visitor Registration');
        $response->assertSee('Personal Profile Information');
        $response->assertSee('Meeting & Destination Assignment', false);
        $response->assertSee('Verification Credentials & Photo Storage', false);
        $response->assertSee('Select Visitor Type');
    }

    /**
     * Test 2: Validation fails when required fields are missing.
     */
    public function test_visitor_registration_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('school.front-desk.visitor-registration.store'), []);

        $response->assertSessionHasErrors([
            'visitor_type',
            'full_name',
            'mobile_number',
            'whom_to_meet_type',
            'security_gate',
            'entourage_count',
            'visit_purpose',
        ]);
    }

    /**
     * Test 3: Visitor registration succeeds with valid payload and generates PASS-{CODE}-XXXX pass.
     */
    public function test_visitor_registration_stores_record_and_generates_pass(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->create('visitor_profile.jpg', 50, 'image/jpeg');

        $payload = [
            'visitor_type'             => 'Parent / Guardian',
            'full_name'                => 'Rajesh Sharma',
            'gender'                   => 'Male',
            'dob'                      => '1985-05-15',
            'mobile_number'            => '9876543210',
            'alternate_mobile'         => '9876543211',
            'email'                    => 'rajesh.sharma@example.com',
            'street_address'           => 'Flat 402, Green Valley Apartments',
            'state'                    => 'Maharashtra',
            'city'                     => 'Mumbai',
            'pincode'                  => '400001',
            'whom_to_meet_type'        => 'Teacher / Faculty',
            'host_name'                => 'Sunita Roy (Class 8 Teacher)',
            'security_gate'            => 'Main Gate 1',
            'entourage_count'          => 2,
            'visit_purpose'            => 'Parent-Teacher Interaction',
            'detailed_purpose_remarks' => 'Discuss term 1 academic performance',
            'id_proof_type'            => 'Aadhaar Card',
            'id_proof_number'          => 'XXXX-XXXX-1234',
            'vehicle_number'           => 'MH-01-AB-1234',
            'photo'                    => $photo,
            'security_notes'           => 'Gatepass issued with 1 accompanying person',
        ];

        $response = $this->actingAs($this->admin)->post(route('school.front-desk.visitor-registration.store'), $payload);

        $response->assertRedirect(route('school.front-desk.visitor-registration'));
        $response->assertSessionHas('success');

        $visitor = Visitor::where('school_id', $this->school->id)->where('mobile_number', '9876543210')->first();
        $this->assertNotNull($visitor);
        $this->assertEquals('Rajesh Sharma', $visitor->full_name);
        $this->assertEquals('Parent / Guardian', $visitor->visitor_type);
        $this->assertEquals('checked_in', $visitor->status);
        $this->assertEquals(2, $visitor->entourage_count);
        $this->assertStringStartsWith('PASS-', $visitor->pass_number);
        $this->assertNotNull($visitor->photo_path);
    }

    /**
     * Test 4: AJAX registration returns pass data for popup scan card.
     */
    public function test_ajax_registration_returns_scan_card_json(): void
    {
        $payload = [
            'visitor_type'      => 'Guest / Dignitary',
            'full_name'         => 'Souhardyadip Mondal',
            'mobile_number'     => '7471515451',
            'whom_to_meet_type' => 'Principal / Management',
            'security_gate'     => 'Main Gate 1',
            'entourage_count'   => 1,
            'visit_purpose'     => 'Meet Principal',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('school.front-desk.visitor-registration.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'visitor' => ['id', 'pass_number', 'full_name'],
            'print_url',
        ]);
    }

    /**
     * Test 5: Mobile lookup endpoint returns returning visitor information.
     */
    public function test_visitor_lookup_by_mobile_returns_data(): void
    {
        $visitor = Visitor::create([
            'school_id'         => $this->school->id,
            'pass_number'       => 'PASS-EDUZEN-0001',
            'visitor_type'      => 'Vendor / Supplier',
            'full_name'         => 'Ramesh Patel',
            'mobile_number'     => '9198765001',
            'whom_to_meet_type' => 'Administration / Front Desk',
            'security_gate'     => 'Gate No. 2 (North)',
            'entourage_count'   => 1,
            'visit_purpose'     => 'Vendor Delivery / Maintenance',
            'city'              => 'Pune',
            'state'             => 'Maharashtra',
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.front-desk.visitor-registration.lookup', ['phone' => '9198765001']));

        $response->assertStatus(200);
        $response->assertJson([
            'found'   => true,
            'visitor' => [
                'full_name'    => 'Ramesh Patel',
                'visitor_type' => 'Vendor / Supplier',
                'city'         => 'Pune',
            ]
        ]);
    }

    /**
     * Test 6: Scanner lookup by QR / Pass reference returns visitor details.
     */
    public function test_scanner_lookup_by_pass_number(): void
    {
        $visitor = Visitor::create([
            'school_id'         => $this->school->id,
            'pass_number'       => 'PASS-EDUZEN-0019',
            'visitor_type'      => 'Visitor',
            'full_name'         => 'Souhardyadip Mondal',
            'mobile_number'     => '7471515451',
            'whom_to_meet_type' => 'Principal / Management',
            'security_gate'     => 'Main Gate 1',
            'entourage_count'   => 1,
            'visit_purpose'     => 'Meet Principal',
            'status'            => 'checked_in',
            'check_in_at'       => now(),
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('school.front-desk.visitor.scan-lookup'), [
            'query' => 'PASS-EDUZEN-0019'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'found' => true,
            'visitor' => [
                'full_name'   => 'Souhardyadip Mondal',
                'pass_number' => 'PASS-EDUZEN-0019',
            ]
        ]);
    }

    /**
     * Test 7: Exit gate check-out process marks visitor as checked_out.
     */
    public function test_scanner_check_out_process(): void
    {
        $visitor = Visitor::create([
            'school_id'         => $this->school->id,
            'pass_number'       => 'PASS-EDUZEN-0019',
            'visitor_type'      => 'Visitor',
            'full_name'         => 'Souhardyadip Mondal',
            'mobile_number'     => '7471515451',
            'whom_to_meet_type' => 'Principal / Management',
            'security_gate'     => 'Main Gate 1',
            'entourage_count'   => 1,
            'visit_purpose'     => 'Meet Principal',
            'status'            => 'checked_in',
            'check_in_at'       => now(),
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('school.front-desk.visitor.check-out.process'), [
            'visitor_id' => $visitor->id
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $visitor->refresh();
        $this->assertEquals('checked_out', $visitor->status);
        $this->assertNotNull($visitor->check_out_at);
    }

    /**
     * Test 9: Public Visitor Self-Registration page loads for valid school.
     */
    public function test_public_visitor_self_registration_page_loads(): void
    {
        $response = $this->get(route('public.visitor.register', ['schoolCode' => $this->school->code ?: $this->school->id]));

        $response->assertStatus(200);
        $response->assertSee($this->school->name);
        $response->assertSee('Visitor Self-Registration');
        $response->assertSee('Personal Information');
    }

    /**
     * Test 10: Public Visitor Self-Registration stores pending request.
     */
    public function test_public_visitor_self_registration_submits_pending_request(): void
    {
        $payload = [
            'visitor_type'      => 'Parent / Guardian',
            'full_name'         => 'Anita Roy',
            'mobile_number'     => '9876543299',
            'email'             => 'anita.roy@example.com',
            'whom_to_meet_type' => 'Principal / Management',
            'visit_purpose'     => 'New Admission Enquiry',
            'entourage_count'   => 1,
        ];

        $response = $this->post(route('public.visitor.register.store', ['schoolCode' => $this->school->code ?: $this->school->id]), $payload);

        $response->assertSessionHas('request_submitted', true);

        $visitor = Visitor::where('school_id', $this->school->id)->where('email', 'anita.roy@example.com')->first();
        $this->assertNotNull($visitor);
        $this->assertEquals('pending', $visitor->status);
        $this->assertTrue($visitor->is_self_registered);
        $this->assertStringStartsWith('PASS-', $visitor->pass_number);
    }

    /**
     * Test 11: School Admin can view Visitor Requests dashboard.
     */
    public function test_school_admin_can_view_visitor_requests(): void
    {
        Visitor::create([
            'school_id'          => $this->school->id,
            'pass_number'        => 'PASS-EDUZEN-0101',
            'visitor_type'       => 'Guest',
            'full_name'          => 'Dr. Karan Singhania',
            'mobile_number'      => '9871112233',
            'email'              => 'karan@example.com',
            'whom_to_meet_type'  => 'Principal / Management',
            'security_gate'      => 'Main Gate 1',
            'entourage_count'    => 1,
            'visit_purpose'      => 'Official Meeting / Inspection',
            'status'             => 'pending',
            'is_self_registered' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.front-desk.visitor-requests'));

        $response->assertStatus(200);
        $response->assertSee('Dr. Karan Singhania');
        $response->assertSee('PASS-EDUZEN-0101');
        $response->assertSee('Pending Requests');
    }

    /**
     * Test 12: School Admin approves request and triggers VisitorPassMail.
     */
    public function test_school_admin_approval_updates_status_and_sends_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $visitor = Visitor::create([
            'school_id'          => $this->school->id,
            'pass_number'        => 'PASS-EDUZEN-0102',
            'visitor_type'       => 'Parent / Guardian',
            'full_name'          => 'Meena Kumari',
            'mobile_number'      => '9870001122',
            'email'              => 'meena.kumari@example.com',
            'whom_to_meet_type'  => 'Teacher / Faculty',
            'security_gate'      => 'Main Gate 1',
            'entourage_count'    => 1,
            'visit_purpose'      => 'Parent-Teacher Interaction',
            'status'             => 'pending',
            'is_self_registered' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('school.front-desk.visitor-requests.approve', $visitor->id));

        $response->assertSessionHas('success');

        $visitor->refresh();
        $this->assertEquals('checked_in', $visitor->status);
        $this->assertNotNull($visitor->approved_at);
        $this->assertEquals($this->admin->id, $visitor->approved_by);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\VisitorPassMail::class, function ($mail) use ($visitor) {
            return $mail->hasTo('meena.kumari@example.com') && $mail->visitor->id === $visitor->id;
        });
    }

    /**
     * Test 13: School Admin rejection updates status to rejected with reason.
     */
    public function test_school_admin_rejection_updates_status(): void
    {
        $visitor = Visitor::create([
            'school_id'          => $this->school->id,
            'pass_number'        => 'PASS-EDUZEN-0103',
            'visitor_type'       => 'Vendor / Supplier',
            'full_name'          => 'Vikram Seth',
            'mobile_number'      => '9873334455',
            'whom_to_meet_type'  => 'Administration / Front Desk',
            'security_gate'      => 'Main Gate 1',
            'entourage_count'    => 1,
            'visit_purpose'      => 'Vendor Delivery / Maintenance',
            'status'             => 'pending',
            'is_self_registered' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('school.front-desk.visitor-requests.reject', $visitor->id), [
            'rejection_reason' => 'Vendor deliveries allowed after 3 PM only.'
        ]);

        $response->assertSessionHas('success');

        $visitor->refresh();
        $this->assertEquals('rejected', $visitor->status);
        $this->assertEquals('Vendor deliveries allowed after 3 PM only.', $visitor->rejection_reason);
    }
}

