<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('unit_id')->index('unit_id')->default(0)->after('purchase_price');
            $table->integer('package_id')->index('package_id')->default(0)->after('unit_id');
            $table->string('sku_code', 20)->index('sku_code')->nullable()->after('package_id');
            $table->string('other_document')->index('other_document')->nullable()->after('sku_code');
            $table->integer('type')->default(0)->comment('0 = product, 1 = service')->after('other_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('unit_id');
            $table->dropColumn('package_id');
            $table->dropColumn('sku_code');
            $table->dropColumn('other_document');
        });
    }
}
