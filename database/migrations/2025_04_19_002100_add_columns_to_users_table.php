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
            $table->foreignId('created_by')->after('id')->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->constrained('users');
            $table->foreignId('company_id')->after('updated_by')->constrained('companies');
            $table->foreignId('role_id')->after('company_id')->constrained('roles');
            $table->boolean('active')->default(1)->after('role_id');
            $table->text('responsabilities')->nullable()->after('active');
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
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('active');
            $table->dropColumn('responsabilities');
            $table->dropSoftDeletes();
        });
    }
};