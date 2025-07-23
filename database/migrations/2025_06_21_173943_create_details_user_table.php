<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('details_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('github_link')->nullable();
            $table->string('website_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('residence')->nullable();
            $table->string('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->text('biography')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details_user');
    }
};
