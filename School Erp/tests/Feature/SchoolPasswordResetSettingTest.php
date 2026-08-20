<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SchoolPasswordResetSettingTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected User $adminA;
    protected User $adminB;
    protected User $studentA;
    protected User $studentB;

    protected function setUp(): void
    {
        parent::setUp();
        app()->forgetInstance('currentSchool');

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Create School A
        $this->schoolA = School::create([
            'name' => 'School Alpha',
            'code' => 'ALPHA',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Admin Alpha',
            'email' => 'admin@alpha.com',
            'phone' => '1111111111',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->adminA->assignRole('admin');

        $this->studentA = User::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Student Alpha',
            'email' => 'student@alpha.com',
            'phone' => '1111111112',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
        ]);
        $this->studentA->assignRole('student');

        // Create School B
        $this->schoolB = School::create([
            'name' => 'School Beta',
            'code' => 'BETA',
            'status' => 'active',
        ]);

        $this->adminB = User::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Admin Beta',
            'email' => 'admin@beta.com',
            'phone' => '2222222221',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->adminB->assignRole('admin');

        $this->studentB = User::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Student Beta',
            'email' => 'student@beta.com',
            'phone' => '2222222222',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
        ]);
        $this->studentB->assignRole('student');
    }

    #[Test]
    public function default_setting_value_is_enabled()
    {
        $value = SettingService::get('allow_password_reset', '1', $this->schoolA->id);
        $this->assertEquals('1', $value);
    }

    #[Test]
    public function setting_toggle_can_be_saved_and_persisted()
    {
        $this->actingAs($this->adminA);

        // Turn OFF
        $response = $this->post(route('school.settings.all.update'), [
            'allow_password_reset' => '0',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('0', SettingService::get('allow_password_reset', '1', $this->schoolA->id));

        // Refresh and check
        $this->assertEquals('0', SettingService::get('allow_password_reset', '1', $this->schoolA->id));

        // Turn ON again
        $response2 = $this->post(route('school.settings.all.update'), [
            'allow_password_reset' => '1',
        ]);

        $response2->assertSessionHasNoErrors();
        $this->assertEquals('1', SettingService::get('allow_password_reset', '1', $this->schoolA->id));
    }

    #[Test]
    public function reset_password_page_opens_normally_when_setting_is_on()
    {
        $this->actingAs($this->adminA);
        SettingService::set('allow_password_reset', '1', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->get(route('school.settings.reset-password'));
        $response->assertStatus(200);
        $response->assertViewHas('allowPasswordReset', true);
        $response->assertDontSeeText('Password Reset has been disabled by your School Administrator.');
    }

    #[Test]
    public function reset_password_page_opens_normally_and_shows_warning_when_setting_is_off()
    {
        $this->actingAs($this->adminA);
        SettingService::set('allow_password_reset', '0', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->get(route('school.settings.reset-password'));
        $response->assertStatus(200);
        $response->assertViewHas('allowPasswordReset', false);
        $response->assertSeeText('Password Reset has been disabled by your School Administrator.');
    }

    #[Test]
    public function password_reset_post_succeeds_when_setting_is_on()
    {
        $this->actingAs($this->adminA);
        SettingService::set('allow_password_reset', '1', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->post(route('school.settings.reset-password.post'), [
            'user_id' => $this->studentA->id,
            'password' => 'NewSecret@123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->studentA->refresh();
        $this->assertTrue(Hash::check('NewSecret@123', $this->studentA->password));
        $this->assertTrue((bool)$this->studentA->must_change_password);
    }

    #[Test]
    public function password_reset_post_fails_when_setting_is_off()
    {
        $this->actingAs($this->adminA);
        SettingService::set('allow_password_reset', '0', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->post(route('school.settings.reset-password.post'), [
            'user_id' => $this->studentA->id,
            'password' => 'HackPassword@123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Password Reset has been disabled by your School Administrator.');

        $this->studentA->refresh();
        $this->assertFalse(Hash::check('HackPassword@123', $this->studentA->password));
    }

    #[Test]
    public function password_reset_ajax_request_returns_json_error_when_disabled()
    {
        $this->actingAs($this->adminA);
        SettingService::set('allow_password_reset', '0', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->postJson(route('school.settings.reset-password.post'), [
            'user_id' => $this->studentA->id,
            'password' => 'HackPassword@123',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Password Reset has been disabled by your School Administrator.',
        ]);
    }

    #[Test]
    public function multi_tenant_isolation_school_a_off_does_not_affect_school_b_on()
    {
        // School A set to OFF
        SettingService::set('allow_password_reset', '0', 'user_control', 'boolean', $this->schoolA->id);

        // School B set to ON
        SettingService::set('allow_password_reset', '1', 'user_control', 'boolean', $this->schoolB->id);

        // School A admin tries resetting Student A password -> Blocked
        $this->actingAs($this->adminA);
        $responseA = $this->post(route('school.settings.reset-password.post'), [
            'user_id' => $this->studentA->id,
            'password' => 'AlphaNewPass@123',
        ]);
        $responseA->assertSessionHas('error');
        $this->studentA->refresh();
        $this->assertFalse(Hash::check('AlphaNewPass@123', $this->studentA->password));

        // School B admin tries resetting Student B password -> Allowed
        $this->actingAs($this->adminB);
        $responseB = $this->post(route('school.settings.reset-password.post'), [
            'user_id' => $this->studentB->id,
            'password' => 'BetaNewPass@123',
        ]);
        $responseB->assertSessionHas('success');
        $this->studentB->refresh();
        $this->assertTrue(Hash::check('BetaNewPass@123', $this->studentB->password));
    }

    #[Test]
    public function login_forgot_password_request_fails_when_setting_is_off()
    {
        SettingService::set('allow_password_reset', '0', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->postJson(route('password.email'), [
            'login_input' => $this->studentA->email,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Password Reset has been disabled by your School Administrator.',
        ]);
    }

    #[Test]
    public function login_forgot_password_request_succeeds_when_setting_is_on()
    {
        SettingService::set('allow_password_reset', '1', 'user_control', 'boolean', $this->schoolA->id);

        $response = $this->postJson(route('password.email'), [
            'login_input' => $this->studentA->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
