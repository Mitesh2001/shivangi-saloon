<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistributorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('enquiry_types', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('statuses', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('branches', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('units', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('packages', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('holidays', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('tags', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('enquiries', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('appointments', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('categories', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('products', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('stock_master', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('stock_income_history', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('stock_edit_history', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('deals_and_discounts', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('orders', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('daybook', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('email_templates', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('clients', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
        Schema::table('users', function (Blueprint $table) { 
            $table->integer('distributor_id')->default(0);   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('enquiry_types', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('statuses', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('branches', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('units', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('packages', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('holidays', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('tags', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('enquiries', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('appointments', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('categories', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('products', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('stock_master', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('stock_income_history', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('stock_edit_history', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('deals_and_discounts', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('orders', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('daybook', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        });
        Schema::table('email_templates', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        }); 
        Schema::table('clients', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        }); 
        Schema::table('users', function (Blueprint $table) { 
            $table->dropColumn('distributor_id');   
        }); 
    }
}
