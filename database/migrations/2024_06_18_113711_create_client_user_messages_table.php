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
        Schema::create('client_user_messages', function (Blueprint $table) {
            $table->id();
            $table->string('t_chat_id',30);
            $table->foreignIdFor(\App\Models\ClientUser::class)
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_user_messages');
    }
};
