<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\WorkType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {

            $table->enum(
                'work_type',
                array_column(WorkType::cases(), 'value')
            )->default(WorkType::FULL_TIME->value)->change();
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('work_type')->change();
        });
    }
};
