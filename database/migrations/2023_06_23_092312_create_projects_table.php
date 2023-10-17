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
        Schema::create('projects', function (Blueprint $table) {
          $table->bigIncrements('id'); // permission id
          $table->string('project_name');
          $table->string('description');
          $table->string('type');
          $table->string('leads');
          $table->string('members');
            $table->timestamps();
            $table->unique(['project_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};