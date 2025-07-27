<?php

use App\Enums\RotationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ProductTypes;
use App\Enums\RotationTypes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');

            $table->foreignId('category_id')->constrained('categories');
            $table->morphs('locationable');

            $table->foreignId('batch_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('statuses')
                ->nullOnDelete();

            $table->string('name')->nullable();

            $table->enum('type', array_keys(ProductTypes::getDescriptiveValues()));
            $table->unsignedSmallInteger('amount')->default(1);

            $table->string('code')->unique();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};