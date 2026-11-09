
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('appointments', 'attendance_status')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('attendance_status')
                    ->default('unconfirmed');
            });
        }

        if (!Schema::hasColumn('appointments', 'attendance_verified_by')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('attendance_verified_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('appointments', 'attendance_verified_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->timestamp('attendance_verified_at')
                    ->nullable();
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty.
        // Avoid removing attendance data during a rollback.
    }
};
