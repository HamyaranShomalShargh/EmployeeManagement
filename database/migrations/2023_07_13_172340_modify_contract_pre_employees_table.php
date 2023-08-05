<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_pre_employees', function (Blueprint $table) {
            $table->dropColumn("to_reload");
            $table->dropColumn("reloaded");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_pre_employees', function (Blueprint $table) {
            $table->boolean("to_reload")->default(0);
            $table->boolean("reloaded")->default(0);
        });
    }
};
