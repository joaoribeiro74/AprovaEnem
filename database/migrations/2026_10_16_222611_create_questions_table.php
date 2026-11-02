<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('number');
            $table->string('discipline');
            $table->string('language', 20)->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('day'); // 1 = questions 1-90, 2 = questions 91-180
            $table->text('context')->nullable();
            $table->text('statement')->nullable();
            $table->char('correct_alternative', 1)->nullable();
            $table->timestamps();

            $table->index(['discipline', 'year']);
            $table->index(['year', 'day']);
        });

        // language is nullable, and PostgreSQL treats two NULLs as different in a unique index
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'CREATE UNIQUE INDEX questions_year_number_language_unique
                ON questions (year, number, language) NULLS NOT DISTINCT'
            );
        } else {
            Schema::table('questions', function (Blueprint $table) {
                $table->unique(['year', 'number', 'language']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
