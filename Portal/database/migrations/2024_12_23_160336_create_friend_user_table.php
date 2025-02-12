<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('friend_user', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->constrained('users')->onDelete('cascade');
        $table->unsignedBigInteger('friend_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();

        $table->unique(['user_id', 'friend_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friend_user');
    }
};
