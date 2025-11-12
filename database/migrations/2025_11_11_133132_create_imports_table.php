<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('import_type');
            $table->string('file_key');
            $table->string('original_file_name')->nullable();
            $table->string('stored_file_path')->nullable();
            $table->enum('status', ['pending','running','completed','failed'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
