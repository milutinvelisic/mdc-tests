<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_id');
            $table->string('file_key')->nullable();
            $table->bigInteger('row_number')->nullable();
            $table->string('column_key')->nullable();
            $table->text('value')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index('import_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_errors');
    }
};
