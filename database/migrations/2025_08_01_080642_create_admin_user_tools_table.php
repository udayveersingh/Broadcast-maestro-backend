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
        Schema::create('admin_user_tools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('tool_id')->nullable();
            $table->string('name')->nullable();
            $table->text('content_prompt')->nullable();
            $table->integer('budget')->default(0);
            $table->integer('deadline')->nullable(); // in days
            $table->string('supplier')->nullable();
            $table->string('target_audience')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_tools');
    }
};
