<?php

namespace App\Observers;

use App\Models\Supplier;

class SupplierObserver
{

    public $afterCommit = true;


    /**
     * Handle the Supplier "created" event.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return void
     */
    public function created(Supplier $supplier){
        $supplier->updated_by = 15; //auth()->user()->id;
       // $supplier->save();
    }

    /**
     * Handle the Supplier "updated" event.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return void
     */
    // public function updated(Supplier $supplier){
    //     $supplier->updated_by = 25; //auth()->user()->id;
    //   //  $supplier->save();
    // }

    

    public function updating(Supplier &$supplier){
        $supplier->updated_by = 125; //auth()->user()->id;

       //  return $supplier;
        // $supplier->save();
    }

    /**
     * Handle the Supplier "deleted" event.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return void
     */
    public function deleted(Supplier $supplier)
    {
        //
    }

    /**
     * Handle the Supplier "restored" event.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return void
     */
    public function restored(Supplier $supplier)
    {
        //
    }

    /**
     * Handle the Supplier "force deleted" event.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return void
     */
    public function forceDeleted(Supplier $supplier)
    {
        //
    }
}
