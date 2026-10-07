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
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        // เชื่อมไปยังตาราง categories (หากหมวดหมู่ถูกลบ โพสต์จะถูกลบตามด้วย cascadeOnDelete)
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->string('title');    // <--- ต้องมีบรรทัดนี้
        $table->text('content');     // <--- ต้องมีบรรทัดนี้
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
