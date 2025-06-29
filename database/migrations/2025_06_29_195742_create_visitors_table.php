// database/migrations/xxxx_create_visitors_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('page_visited');
            $table->string('referrer')->nullable();
            $table->timestamp('visited_at');
            $table->date('visit_date');
            $table->timestamps();
            
            $table->index(['ip_address', 'visit_date']);
            $table->index('visit_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
    }
}