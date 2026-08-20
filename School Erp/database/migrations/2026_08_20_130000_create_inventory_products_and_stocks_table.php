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
        if (!Schema::hasTable('inventory_products')) {
            Schema::create('inventory_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
                $table->string('name')->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->decimal('mrp', 10, 2)->default(0.00);
                $table->decimal('tax', 8, 2)->default(0.00);
                $table->boolean('status')->default(true);
                $table->string('size_type')->default('none'); // 'none', 's_xxl', 'chart_1_11', 'chart_24_44'
                $table->json('selected_sizes')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'status']);
                $table->index(['school_id', 'category_id']);
            });
        }

        if (!Schema::hasTable('inventory_stocks')) {
            Schema::create('inventory_stocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('inventory_products')->onDelete('cascade');
                $table->string('size')->default('Free');
                $table->integer('stock')->default(0);
                $table->decimal('price', 10, 2)->nullable();
                $table->decimal('mrp', 10, 2)->nullable();
                $table->timestamps();

                $table->index(['school_id', 'product_id']);
            });
        }

        if (!Schema::hasTable('inventory_stock_logs')) {
            Schema::create('inventory_stock_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('inventory_products')->onDelete('cascade');
                $table->string('size')->nullable();
                $table->string('type')->default('in'); // 'in', 'out', 'adjustment'
                $table->integer('quantity')->default(0);
                $table->integer('stock_before')->default(0);
                $table->integer('stock_after')->default(0);
                $table->string('remarks')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_logs');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_products');
    }
};
