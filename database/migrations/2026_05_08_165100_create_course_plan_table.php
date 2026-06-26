<?php

use App\Models\Course;
use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_plan', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Course::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Plan::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_plan');
    }
};
