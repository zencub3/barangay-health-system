
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('attendance_status')->default('unconfirmed');

                $table->foreignId('attendance_verified_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('attendance_verified_at')->nullable();
            });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_verified_by');

            $table->dropColumn([
                'attendance_status',
                'attendance_verified_at',
            ]);
        });
    }
};
