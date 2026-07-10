<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create cancelled_payments table
        Schema::create('cancelled_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('receipt_number');
            $table->string('student_name');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('reason');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // 2. Create installment_edit_histories table
        Schema::create('installment_edit_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('student_name');
            $table->string('field');
            $table->string('old_value');
            $table->string('new_value');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // 3. Create deleted_fines table
        Schema::create('deleted_fines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('fine_name');
            $table->string('deleted_by');
            $table->date('date');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // 4. Create deleted_concessions table
        Schema::create('deleted_concessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('concession_name');
            $table->string('deleted_by');
            $table->date('date');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // Seed some realistic data for school_id = 1 (and others if needed) to ensure the reports are populated and functional
        $schoolId = 1;
        $now = now();

        // Check if school 1 exists before seeding to avoid foreign key violation
        if (DB::table('schools')->where('id', $schoolId)->exists()) {
            // Cancelled payments mock data
            DB::table('cancelled_payments')->insert([
                [
                    'school_id' => $schoolId,
                    'receipt_number' => 'REC-829104',
                    'student_name' => 'Aarav Sharma',
                    'payment_date' => '2026-06-15',
                    'amount' => 4500.00,
                    'reason' => 'Cheque bounced due to insufficient funds',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'school_id' => $schoolId,
                    'receipt_number' => 'REC-901234',
                    'student_name' => 'Isha Patel',
                    'payment_date' => '2026-06-20',
                    'amount' => 2500.00,
                    'reason' => 'Incorrect fee category assigned; re-generated new receipt',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);

            // Installment edit histories mock data
            DB::table('installment_edit_histories')->insert([
                [
                    'school_id' => $schoolId,
                    'student_name' => 'Kabir Mehta',
                    'field' => 'Term 1 Installment Amount',
                    'old_value' => '₹ 15,000.00',
                    'new_value' => '₹ 12,000.00',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'school_id' => $schoolId,
                    'student_name' => 'Diya Sen',
                    'field' => 'Transport Fee Installment 2',
                    'old_value' => '₹ 3,500.00',
                    'new_value' => '₹ 4,000.00',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);

            // Deleted fines mock data
            DB::table('deleted_fines')->insert([
                [
                    'school_id' => $schoolId,
                    'fine_name' => 'Late Library Return Fine (Daily)',
                    'deleted_by' => 'Dr. R. K. Singh',
                    'date' => '2026-06-10',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'school_id' => $schoolId,
                    'fine_name' => 'Uniform Violation Fine (Fixed)',
                    'deleted_by' => 'Admin User',
                    'date' => '2026-06-25',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);

            // Deleted concessions mock data
            DB::table('deleted_concessions')->insert([
                [
                    'school_id' => $schoolId,
                    'concession_name' => 'Covid Relief Special Discount',
                    'deleted_by' => 'Admin User',
                    'date' => '2026-06-01',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'school_id' => $schoolId,
                    'concession_name' => 'Sibling Discount Tier 3',
                    'deleted_by' => 'Accountant (Mr. Gupta)',
                    'date' => '2026-06-18',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_payments');
        Schema::dropIfExists('installment_edit_histories');
        Schema::dropIfExists('deleted_fines');
        Schema::dropIfExists('deleted_concessions');
    }
};
