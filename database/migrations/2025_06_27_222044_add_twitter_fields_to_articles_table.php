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
        Schema::table('articles', function (Blueprint $table) {
           $table->string('twitter_id')->nullable()->unique()->after('id');
            $table->enum('source', ['MANUAL', 'TWITTER'])->default('MANUAL')->after('status');
            $table->json('twitter_data')->nullable()->after('source');
            $table->string('featured_image')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
           $table->dropColumn(['twitter_id', 'source', 'twitter_data', 'featured_image']);
        });
    }
};
