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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('slug');
            $table->string('tagline')->nullable()->after('logo_path');
            $table->text('description')->nullable()->after('tagline');
            $table->string('public_owner_name')->nullable()->after('description');
            $table->string('public_email')->nullable()->after('public_owner_name');
            $table->string('public_phone')->nullable()->after('public_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'tagline',
                'description',
                'public_owner_name',
                'public_email',
                'public_phone',
            ]);
        });
    }
};
