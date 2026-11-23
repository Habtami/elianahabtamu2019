<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->nullable();
            $table->string('department')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('password_changed_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('activity_type');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->string('field_name')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('edited_by')->nullable();
            $table->string('severity')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    private function flaggedUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Temp User',
            'email' => 'temp.user@example.com',
            'password' => Hash::make('Temp@1234'),
            'role' => 'cmd',
            'department' => 'cmd',
            'status' => true,
            'must_change_password' => true,
        ], $overrides));
    }

    public function test_flagged_user_is_redirected_to_password_change_page(): void
    {
        $this->actingAs($this->flaggedUser());

        $this->get('/dashboard')->assertRedirect(route('password.change'));
        $this->get('/contracts')->assertRedirect(route('password.change'));
        $this->get('/profile')->assertRedirect(route('password.change'));
    }

    public function test_password_change_page_renders_with_warning(): void
    {
        $this->actingAs($this->flaggedUser());

        $this->get(route('password.change'))
            ->assertOk()
            ->assertSee('Password change required')
            ->assertSee('እባክዎ የመጀመሪያ ጊዜ ፓስዎርድዎን ይቀይሩ።');
    }

    public function test_login_redirects_flagged_user_to_password_change(): void
    {
        $this->flaggedUser(['email' => 'login.temp@example.com']);

        $response = $this->post('/login', [
            'email' => 'login.temp@example.com',
            'password' => 'Temp@1234',
        ]);

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHas('warning');
    }

    public function test_user_can_change_password_and_flag_is_cleared(): void
    {
        $user = $this->flaggedUser();
        $this->actingAs($user);

        $response = $this->post(route('password.change.update'), [
            'current_password' => 'Temp@1234',
            'new_password' => 'NewPass@2026',
            'new_password_confirmation' => 'NewPass@2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'የይለፍ ቃልዎ በተሳካ ሁኔታ ተቀይሯል። እባክዎ በአዲሱ የይለፍ ቃልዎ ይግቡ።');

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('NewPass@2026', $user->password));
        $this->assertGuest();

        $this->get(route('password.change'))->assertRedirect(route('login'));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = $this->flaggedUser();
        $this->actingAs($user);

        $this->from(route('password.change'))
            ->post(route('password.change.update'), [
                'current_password' => 'Wrong@1234',
                'new_password' => 'NewPass@2026',
                'new_password_confirmation' => 'NewPass@2026',
            ])
            ->assertRedirect(route('password.change'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue($user->fresh()->must_change_password);
    }

    public function test_weak_new_password_is_rejected(): void
    {
        $user = $this->flaggedUser();
        $this->actingAs($user);

        $this->from(route('password.change'))
            ->post(route('password.change.update'), [
                'current_password' => 'Temp@1234',
                'new_password' => 'weak',
                'new_password_confirmation' => 'weak',
            ])
            ->assertSessionHasErrors('new_password');

        $this->assertTrue($user->fresh()->must_change_password);
    }

    public function test_reset_command_sets_configured_default_for_all_users(): void
    {
        config(['app.default_password' => 'Configured@2026']);

        $first = $this->flaggedUser();
        $second = User::create([
            'name' => 'Active User',
            'email' => 'active.user@example.com',
            'password' => Hash::make('Custom@2026'),
            'role' => 'cmd',
            'department' => 'cmd',
            'status' => true,
            'must_change_password' => false,
        ]);

        $this->artisan('users:reset-password')->assertSuccessful();

        foreach ([$first, $second] as $user) {
            $user->refresh();
            $this->assertTrue($user->must_change_password);
            $this->assertTrue(Hash::check(config('app.default_password'), $user->password));
        }
    }
}
