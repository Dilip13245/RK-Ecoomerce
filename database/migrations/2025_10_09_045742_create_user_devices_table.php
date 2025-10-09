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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token', 255)->nullable();
            $table->enum('device_type', ['A', 'I'])->comment('A = Android, I = iOS');
            $table->string('ip_address', 50)->nullable();
            $table->string('uuid', 100)->nullable();
            $table->string('os_version', 100)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('app_version', 50)->nullable();
            $table->string('device_token', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};