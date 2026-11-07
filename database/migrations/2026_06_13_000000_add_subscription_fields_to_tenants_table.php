<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('receipt_status')->default('none'); // none, pending, approved, rejected
            $table->string('selected_plan')->nullable(); // 1_month, 3_months, 1_year
            $table->timestamp('receipt_uploaded_at')->nullable();
        });

        // Initialize trial_ends_at for existing tenants to created_at + 1 month
        $tenants = DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            $createdAt = $tenant->created_at ? Carbon::parse($tenant->created_at) : Carbon::now();
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'trial_ends_at' => $createdAt->addMonth()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'trial_ends_at',
                'subscription_ends_at',
                'receipt_path',
                'receipt_status',
                'selected_plan',
                'receipt_uploaded_at',
            ]);
        });
    }
};
