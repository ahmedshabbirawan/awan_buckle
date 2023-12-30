<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        /*
        id, rec_by_name, rec_by_designation, rec_by_cnic, rec_by_phone, 

hand_name, hand_designation, hand_cnic, hand_phone 

purchased_date, project_id, project_dg, 

po_loa_loi, amount_category, delivery_amount, supplier_id, delivery_challan_no, warehouse_id

stock_ledger_reference
page_number
ledger_copy
purschase_copy
delivery_challan_copy
inspection_report_copy
        */
        Schema::create('stock_deliveries', function (Blueprint $table) {
            $table->id();
            
            $table->string("ref_no",255)->nullable();

            $table->string("rec_by_name",255)->nullable();
            $table->string("rec_by_designation",255)->nullable();
            $table->string("rec_by_cnic",255)->nullable();
            $table->string("rec_by_phone",255)->nullable();

            $table->string("hand_name",255)->nullable();
            $table->string("hand_designation",255)->nullable();
            $table->string("hand_cnic",255)->nullable();
            $table->string("hand_phone",255)->nullable();

            $table->date("purchased_date")->nullable();
            $table->integer("project_id")->nullable();
            $table->integer("project_dg")->nullable();


            $table->string("po_loa_loi",255)->nullable();
            $table->string("amount_category",255)->nullable();
            $table->string("delivery_amount",50)->nullable();
            $table->integer("supplier_id")->nullable();
            $table->string("delivery_challan_no",255)->nullable();
            $table->integer("warehouse_id")->nullable();


            $table->string("stock_ledger_reference",255)->nullable();
            $table->string("page_number",255)->nullable();
            $table->string("ledger_copy_file",255)->nullable();
            $table->string("purschase_copy_file",255)->nullable();
            $table->string("delivery_challan_copy_file",255)->nullable();
            $table->string("inspection_report_copy_file",255)->nullable();


            $table->integer("status")->default(1);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_deliveries');
    }
}
