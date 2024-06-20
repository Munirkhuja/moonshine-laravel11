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
        Schema::create('client_registers', function (Blueprint $table) {
            $table->id();
            $table->string('t_chat_id',30);
            $table->string('phone', 30);
            $table->string('code', 10);
            $table->tinyInteger('count')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_registers');
    }
};
