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
        Schema::create('document_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->primary(['document_id', 'tag_id']);

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

            $table->index(['document_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_tag');
    }
};
