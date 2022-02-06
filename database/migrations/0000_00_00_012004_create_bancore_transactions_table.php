<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancoreTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancore_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->unique()->index();
            $table->uuid('processing_item_id')->index();
            $table->uuid('bancore_sender_id')->unique()->index();
            $table->uuid('bancore_recipient_id')->unique()->index();
            $table->uuid('bancore_quota_id')->nullable()->unique()->index();
            $table->uuid('identifier_source_id');
            $table->string('identifier_source_type');

            $table->string('state_code');
            $table->longText('state_code_reason')->nullable();

            $table->string('error_code')->nullable();
            $table->longText('error_code_description')->nullable();

            $table->string('reference')->unique();
            $table->char('receive_currency_code', 3);
            $table->double('receive_amount');

            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->foreign('bancore_sender_id')->references('id')->on('bancore_senders');
            $table->foreign('bancore_recipient_id')->references('id')->on('bancore_recipients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancore_transactions');
    }
}
