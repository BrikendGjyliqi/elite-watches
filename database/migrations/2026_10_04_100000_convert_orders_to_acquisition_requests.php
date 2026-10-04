<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Orders become "acquisition requests": reviewed by the atelier, settled offline.
 */
return new class extends Migration
{
    private const STATUSES = [
        'requested', 'under_review', 'approved', 'declined', 'awaiting_payment',
        'paid', 'shipped', 'delivered', 'cancelled',
    ];

    private const LEGACY_STATUSES = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];

    public function up(): void
    {
        // Widen first so both vocabularies are valid while existing rows are remapped.
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', array_values(array_unique([...self::LEGACY_STATUSES, ...self::STATUSES])))
                ->default('requested')
                ->change();
        });

        DB::table('orders')->where('status', 'pending')->update(['status' => 'requested']);
        DB::table('order_status_histories')->where('from_status', 'pending')->update(['from_status' => 'requested']);
        DB::table('order_status_histories')->where('to_status', 'pending')->update(['to_status' => 'requested']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', self::STATUSES)->default('requested')->change();

            $table->enum('preferred_method', ['wire', 'boutique', 'financing', 'crypto'])->nullable()->after('status');
            $table->text('customer_note')->nullable()->after('notes');
            $table->text('admin_response')->nullable()->after('customer_note');
            $table->decimal('original_total', 10, 2)->nullable()->after('total');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->text('declined_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            // Soft "being reviewed by" marker so two admins don't handle the same request.
            $table->foreignId('reviewing_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewing_started_at')->nullable();

            $table->index(['status', 'created_at']);
        });

        Schema::create('order_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('author_role', ['customer', 'atelier']);
            $table->text('body');
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_messages');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropConstrainedForeignId('reviewing_by');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'preferred_method', 'customer_note', 'admin_response', 'original_total',
                'approved_at', 'declined_at', 'declined_reason', 'reviewing_started_at',
            ]);

            $table->enum('status', array_values(array_unique([...self::LEGACY_STATUSES, ...self::STATUSES])))
                ->default('pending')
                ->change();
        });

        DB::table('orders')->whereIn('status', ['requested', 'under_review', 'approved', 'awaiting_payment'])->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'declined')->update(['status' => 'cancelled']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', self::LEGACY_STATUSES)->default('pending')->change();
        });
    }
};
