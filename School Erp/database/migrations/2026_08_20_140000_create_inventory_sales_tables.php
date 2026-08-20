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
        if (!Schema::hasTable('inventory_sales')) {
            Schema::create('inventory_sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                $table->string('invoice_number')->unique();
                $table->string('receipt_number')->nullable();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->string('admission_no')->nullable();
                $table->string('customer_name');
                $table->text('customer_address')->nullable();
                $table->string('customer_mobile')->nullable();
                $table->string('payment_mode')->default('cash'); // 'cash', 'upi', 'card', 'cheque', 'dd', 'bank_transfer'
                $table->string('reference_no')->nullable();
                $table->decimal('total_mrp', 12, 2)->default(0.00);
                $table->decimal('sub_total', 12, 2)->default(0.00); // total price
                $table->decimal('total_tax', 12, 2)->default(0.00);
                $table->decimal('total_discount', 12, 2)->default(0.00);
                $table->decimal('grand_total', 12, 2)->default(0.00);
                $table->decimal('paid_amount', 12, 2)->default(0.00);
                $table->decimal('due_amount', 12, 2)->default(0.00);
                $table->string('status')->default('completed'); // 'completed', 'cancelled', 'refunded'
                $table->dateTime('sale_date')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'created_at']);
                $table->index(['school_id', 'admission_no']);
                $table->index(['school_id', 'status']);
            });
        }

        if (!Schema::hasTable('inventory_sale_items')) {
            Schema::create('inventory_sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained('inventory_sales')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained('inventory_products')->nullOnDelete();
                $table->string('product_name');
                $table->string('size')->default('Free');
                $table->decimal('mrp', 10, 2)->default(0.00);
                $table->decimal('price', 10, 2)->default(0.00);
                $table->decimal('tax_percent', 8, 2)->default(0.00);
                $table->decimal('tax_amount', 10, 2)->default(0.00);
                $table->integer('quantity')->default(1);
                $table->decimal('discount', 10, 2)->default(0.00);
                $table->decimal('total_mrp', 12, 2)->default(0.00);
                $table->decimal('total_price', 12, 2)->default(0.00);
                $table->decimal('total_tax', 12, 2)->default(0.00);
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->timestamps();

                $table->index(['sale_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_sale_items');
        Schema::dropIfExists('inventory_sales');
    }
};
