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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('orchestra_id')->nullable()->constrained()->cascadeOnDelete();

            $table->enum('role', [
                'admin',
                'member'
            ])->default('member');

            $table->enum('member_type', [
                'core',
                'substitute',
                'guest'
            ])->nullable();

            $table->string('instrument')->nullable();

            $table->enum('section', [
                'violin_1',
                'violin_2',
                'viola',
                'cello',
                'double_bass',
                'french_horn',
                'trumpet',
                'trombone',
                'tuba',
                'flute',
                'oboe',
                'clarinet',
                'bassoon',
                'percussion',
                'mallet',
                'vocal',
                'other'
            ])->nullable();

            $table->date('joined_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'orchestra_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
