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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('created_by')->after('id')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->after('updated_by')->nullable()->constrained('users');
            $table->boolean('active')->default(1)->after('deleted_by');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });
    }
};