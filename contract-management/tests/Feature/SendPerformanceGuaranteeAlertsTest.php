<?php

namespace Tests\Feature;

use App\Mail\PerformanceGuaranteeAlertMail;
use App\Models\Contract;
use App\Models\User;
use App\Rules\GuaranteeAlertDays;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SendPerformanceGuaranteeAlertsTest extends TestCase
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

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('department');
            $table->string('contract_type')->nullable();
            $table->string('material_group')->nullable();
            $table->decimal('contract_amount', 15, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('alert_days');
            $table->string('performance_bond_type')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('currency_type')->nullable();
            $table->decimal('performance_guarantee_amount', 15, 2)->nullable();
            $table->date('performance_guarantee_end_date')->nullable();
            $table->integer('alert_days_before_guarantee_end')->nullable();
            $table->timestamp('guarantee_alert_sent_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('status')->default('active');
            $table->integer('extended_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        config([
            'services.legal_system.url' => null,
            'services.legal_system.token' => null,
            'services.legal_system.fallback_email' => 'project.manager@example.com',
            'services.legal_system.test_recipient' => null,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-24 08:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function makeContract(array $overrides = []): Contract
    {
        $creator = User::create([
            'name' => 'Contract Officer',
            'email' => 'officer@example.com',
            'password' => Hash::make('Secret@1234'),
            'role' => 'cmd',
            'department' => 'cmd',
            'status' => true,
        ]);

        return Contract::create(array_merge([
            'supplier_name' => 'Acme Trading',
            'name' => 'Supply of Medicines',
            'department' => 'cmd',
            'contract_amount' => 250000,
            'currency_type' => 'ETB',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'alert_days' => 30,
            'performance_guarantee_end_date' => '2026-12-24',
            'alert_days_before_guarantee_end' => 30,
            'created_by' => $creator->id,
            'status' => 'active',
        ], $overrides));
    }

    public function test_command_sends_the_email_on_the_exact_alert_day(): void
    {
        Mail::fake();

        $contract = $this->makeContract();

        $this->artisan('contracts:send-guarantee-alerts')->assertSuccessful();

        Mail::assertQueued(PerformanceGuaranteeAlertMail::class, 1);
        Mail::assertQueued(
            PerformanceGuaranteeAlertMail::class,
            fn (PerformanceGuaranteeAlertMail $mail) => $mail->contract->is($contract)
                && $mail->hasTo('project.manager@example.com')
        );

        $this->assertNotNull($contract->fresh()->guarantee_alert_sent_at);
    }

    public function test_command_does_not_send_before_the_alert_day(): void
    {
        Mail::fake();

        $contract = $this->makeContract([
            'performance_guarantee_end_date' => '2027-01-24',
        ]);

        $this->artisan('contracts:send-guarantee-alerts')->assertSuccessful();

        Mail::assertNothingQueued();
        $this->assertNull($contract->fresh()->guarantee_alert_sent_at);
    }

    public function test_command_does_not_send_the_same_alert_twice(): void
    {
        Mail::fake();

        $this->makeContract();

        $this->artisan('contracts:send-guarantee-alerts')->assertSuccessful();
        $this->artisan('contracts:send-guarantee-alerts')->assertSuccessful();

        Mail::assertQueued(PerformanceGuaranteeAlertMail::class, 1);
    }

    public function test_zero_alert_days_sends_on_the_guarantee_end_date(): void
    {
        Mail::fake();

        $contract = $this->makeContract([
            'performance_guarantee_end_date' => '2026-11-24',
            'alert_days_before_guarantee_end' => 0,
        ]);

        $this->artisan('contracts:send-guarantee-alerts')->assertSuccessful();

        Mail::assertQueued(PerformanceGuaranteeAlertMail::class, 1);
        $this->assertNotNull($contract->fresh()->guarantee_alert_sent_at);
    }

    public function test_alert_days_cannot_exceed_the_days_left_before_the_guarantee_end(): void
    {
        $rules = fn (?string $endDate) => [
            'days' => ['nullable', 'integer', 'min:0', 'max:365', new GuaranteeAlertDays($endDate)],
        ];

        $tenDaysAhead = Carbon::today()->addDays(10)->toDateString();

        $this->assertFalse(Validator::make(['days' => 10], $rules($tenDaysAhead))->fails());

        $validator = Validator::make(['days' => 11], $rules($tenDaysAhead));
        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('cannot be greater than', $validator->errors()->first('days'));
    }

    public function test_alert_days_remain_editable_for_an_expired_guarantee(): void
    {
        $expired = Carbon::today()->subDays(5)->toDateString();

        $validator = Validator::make(['days' => 30], [
            'days' => ['nullable', 'integer', 'min:0', 'max:365', new GuaranteeAlertDays($expired)],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_test_alert_command_sends_immediately_without_queueing(): void
    {
        Mail::fake();

        config(['services.legal_system.test_recipient' => 'tester@example.com']);

        $contract = $this->makeContract([
            'name' => 'Test Contract for Email Notification',
            'guarantee_alert_sent_at' => null,
        ]);

        $this->artisan('contracts:send-test-alert')->assertSuccessful();

        Mail::assertSent(
            PerformanceGuaranteeAlertMail::class,
            fn (PerformanceGuaranteeAlertMail $mail) => $mail->contract->is($contract)
                && $mail->hasTo('tester@example.com')
        );
        Mail::assertNothingQueued();
        $this->assertNull($contract->fresh()->guarantee_alert_sent_at);
    }

    public function test_test_alert_command_requires_the_seeded_contract(): void
    {
        Mail::fake();

        config(['services.legal_system.test_recipient' => 'tester@example.com']);

        $this->artisan('contracts:send-test-alert')->assertFailed();

        Mail::assertNothingSent();
    }
}
