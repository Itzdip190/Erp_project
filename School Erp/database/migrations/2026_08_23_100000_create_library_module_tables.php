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
        // 1. Library Rules & Configuration (per school, Student & Staff policies)
        if (!Schema::hasTable('library_rules')) {
            Schema::create('library_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->string('member_type', 30); // 'student', 'staff'
                
                // Borrow Limits & Durations
                $table->unsignedInteger('borrow_period_days')->default(14); // Days
                $table->unsignedInteger('max_books_allowed')->default(3);   // Max books
                
                // Fines & Penalties
                $table->decimal('late_fine_amount', 10, 2)->default(5.00);
                $table->string('late_fine_type', 30)->default('per_day'); // 'per_day', 'fixed_amount', 'per_week'
                
                $table->decimal('lost_book_fine_amount', 10, 2)->default(200.00);
                $table->string('lost_book_fine_type', 40)->default('fixed_amount'); // 'fixed_amount', 'percentage_price', 'full_price_plus_fee'
                
                $table->decimal('damaged_book_fine_amount', 10, 2)->default(100.00);
                $table->string('damaged_book_fine_type', 40)->default('fixed_amount'); // 'fixed_amount', 'percentage_price'
                
                // Additional policies
                $table->unsignedInteger('grace_period_days')->default(0);
                $table->unsignedInteger('max_renewal_count')->default(2);
                $table->boolean('allow_issue_with_fine')->default(false);
                $table->decimal('max_fine_threshold', 10, 2)->default(100.00);
                
                $table->json('extra_config')->nullable();
                $table->timestamps();

                $table->unique(['school_id', 'member_type']);
            });
        }

        // 2. Library Sections / Shelf Categories
        if (!Schema::hasTable('library_sections')) {
            Schema::create('library_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->string('name', 150);
                $table->string('code', 50)->nullable();
                $table->string('rack_location', 100)->nullable(); // e.g. Rack A1-A4, Shelf 3
                $table->string('material_scope', 150)->nullable(); // e.g. Science, Literature, Reference
                $table->text('description')->nullable();
                $table->string('status', 20)->default('active'); // active, inactive
                $table->timestamps();

                $table->index(['school_id', 'status']);
            });
        }

        // 3. Book / Material Types (e.g. Textbook, Reference, Journal, CD/DVD)
        if (!Schema::hasTable('library_book_types')) {
            Schema::create('library_book_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->string('name', 150);
                $table->string('code', 50)->nullable();
                $table->unsignedInteger('default_borrow_days_override')->nullable();
                $table->decimal('fine_multiplier', 5, 2)->default(1.00);
                $table->string('icon', 100)->nullable();
                $table->text('description')->nullable();
                $table->string('status', 20)->default('active');
                $table->timestamps();

                $table->index(['school_id', 'status']);
            });
        }

        // 4. Library Books Inventory / Catalogue
        if (!Schema::hasTable('library_books')) {
            Schema::create('library_books', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->string('accession_no', 100)->nullable();
                $table->string('isbn', 50)->nullable();
                $table->string('title', 255);
                $table->string('author', 200)->nullable();
                $table->string('publisher', 200)->nullable();
                $table->string('edition', 50)->nullable();
                $table->string('publication_year', 20)->nullable();
                $table->foreignId('section_id')->nullable()->constrained('library_sections')->nullOnDelete();
                $table->foreignId('book_type_id')->nullable()->constrained('library_book_types')->nullOnDelete();
                $table->string('language', 50)->default('English');
                $table->unsignedInteger('pages')->nullable();
                $table->string('rack_location', 100)->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->unsignedInteger('total_copies')->default(1);
                $table->unsignedInteger('available_copies')->default(1);
                $table->unsignedInteger('issued_copies')->default(0);
                $table->unsignedInteger('lost_copies')->default(0);
                $table->unsignedInteger('damaged_copies')->default(0);
                $table->string('cover_image', 255)->nullable();
                $table->text('description')->nullable();
                $table->string('status', 30)->default('available'); // available, archived, out_of_stock
                $table->timestamps();

                $table->index(['school_id', 'title']);
                $table->index(['school_id', 'accession_no']);
            });
        }

        // 5. Library Borrows / Issue-Return Transactions
        if (!Schema::hasTable('library_transactions')) {
            Schema::create('library_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->string('transaction_code', 50)->index();
                $table->foreignId('book_id')->constrained('library_books')->onDelete('cascade');
                $table->string('member_type', 30); // student, staff
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
                $table->date('issue_date');
                $table->date('due_date');
                $table->date('return_date')->nullable();
                $table->unsignedInteger('renewed_count')->default(0);
                $table->string('status', 30)->default('issued'); // issued, returned, overdue, lost, damaged
                $table->unsignedInteger('late_days')->default(0);
                $table->decimal('late_fine_amount', 10, 2)->default(0.00);
                $table->decimal('damage_lost_fine', 10, 2)->default(0.00);
                $table->decimal('total_fine', 10, 2)->default(0.00);
                $table->string('fine_status', 30)->default('none'); // none, pending, paid, waived
                $table->date('payment_date')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['school_id', 'status']);
                $table->index(['school_id', 'member_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_transactions');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('library_book_types');
        Schema::dropIfExists('library_sections');
        Schema::dropIfExists('library_rules');
    }
};
