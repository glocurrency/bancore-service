<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancoreMobileMoneyProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancore_mobile_money_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mobile_money_provider_id')->unique()->index();
            $table->string('code');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancore_mobile_money_providers');
    }
}
