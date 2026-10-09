<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // ป้องกันผู้ใช้เดิมกดไลก์ซ้ำ
            $table->index(['post_id', 'user_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};