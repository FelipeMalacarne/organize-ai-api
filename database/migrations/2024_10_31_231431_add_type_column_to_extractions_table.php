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
        Schema::table('extractions', function (Blueprint $table) {
            $table->string('type')->after('document_id')->nullable();
            $table->text('extracted_text')->nullable()->change();
            $table->json('extracted_json')->after('extracted_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extractions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->text('extracted_text')->change();
            $table->dropColumn('extracted_json');
        });
    }
};
