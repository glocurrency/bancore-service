<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancoreQuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancore_quotas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bancore_transaction_id')->unique()->index();

            $table->string('error_code')->nullable();
            $table->longText('error_code_description')->nullable();

            $table->string('reference');
            $table->double('rate')->nullable();
            $table->char('send_currency_code', 3);
            $table->double('send_amount');
            $table->char('receive_currency_code', 3);
            $table->double('receive_amount')->nullable();

            $table->timestamp('expires_at', 6)->nullable();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->foreign('bancore_transaction_id')->references('id')->on('bancore_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancore_quotas');
    }
}
