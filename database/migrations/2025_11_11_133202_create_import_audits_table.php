<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_id')->nullable();
            $table->string('table_name')->nullable();
            $table->unsignedBigInteger('row_id')->nullable();
            $table->string('column_key')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();

            $table->index('import_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_audits');
    }
};
