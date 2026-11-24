<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DriverHistoryTest extends TestCase
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
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('field_name')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('edited_by')->nullable();
            $table->string('severity')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->date('license_expiry')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tires', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number');
            $table->string('tire_type')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tire_gives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tire_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('given_by')->nullable();
            $table->date('give_date');
            $table->integer('given_kilometer');
            $table->string('plate_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tire_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tire_give_id');
            $table->unsignedBigInteger('tire_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->date('return_date');
            $table->integer('current_kilometer');
            $table->integer('registered_kilometer');
            $table->integer('km_difference');
            $table->string('tire_type')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function test_driver_changes_are_persisted_and_returned_in_history(): void
    {
        $user = User::create([
            'name' => 'History Tester',
            'email' => 'history.tester@example.com',
            'password' => Hash::make('Temp@1234'),
            'role' => 'super_admin',
            'department' => 'cmd',
            'status' => true,
        ]);

        $this->actingAs($user);

        $driver = Driver::create([
            'name' => 'Original Driver',
            'phone' => '0900000000',
            'status' => true,
            'created_by' => $user->id,
        ]);

        $driver->update([
            'name' => 'Updated Driver',
            'phone' => '0911111111',
        ]);
        $driver->update(['status' => false]);

        $response = $this->getJson(route('drivers.history', $driver))->assertOk();

        $response->assertJsonPath('summary.changes', 4)
            ->assertJsonPath('summary.assignments', 0)
            ->assertJsonCount(4, 'history');

        $types = collect($response->json('history'))->pluck('type');
        $this->assertContains('created', $types);
        $this->assertSame(2, $types->filter(fn ($type) => $type === 'updated')->count());
        $this->assertContains('status', $types);

        $phoneChange = collect($response->json('history'))
            ->first(fn (array $event) => $event['title'] === 'Phone changed');
        $this->assertSame('0900000000', $phoneChange['meta']['Before']);
        $this->assertSame('0911111111', $phoneChange['meta']['After']);
        $this->assertSame('History Tester', $phoneChange['user']);

        $driver->delete();

        $logs = ActivityLog::where('driver_id', $driver->id)->orderBy('id')->get();
        $this->assertCount(5, $logs);
        $this->assertSame(ActivityLog::TYPE_DRIVER_CREATE, $logs[0]->activity_type);
        $this->assertSame(ActivityLog::TYPE_DRIVER_UPDATE, $logs[1]->activity_type);
        $this->assertSame(ActivityLog::TYPE_DRIVER_STATUS, $logs[3]->activity_type);
        $this->assertSame('Active', $logs[3]->old_value);
        $this->assertSame('Inactive', $logs[3]->new_value);
        $this->assertSame(ActivityLog::TYPE_DRIVER_DELETE, $logs[4]->activity_type);
        $this->assertSame($user->id, $logs[4]->user_id);
    }
}
