<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHistorySummaryUrlToSimulationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('simulations', 'history_summary_url')) {
            return;
        }

        Schema::table('simulations', function (Blueprint $table) {
            $table->string('history_summary_url', 512)->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('simulations', function (Blueprint $table) {
            $table->dropColumn('history_summary_url');
        });
    }
}
