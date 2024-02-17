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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('title');
            $table->string('subtitle');
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('sold_count')->default(0);
            $table->foreignId('user_id')->nullable()
                ->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('published_at')->nullable();
            $table->datetime('time_to_use');
            $table->text('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
