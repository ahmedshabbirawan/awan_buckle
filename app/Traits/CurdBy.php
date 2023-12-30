<?php 
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

use Attribute;



  trait CurdBy{

    protected static function curdBy() {
        parent::boot();

        static::creating(function (&$model) {
            $model->created_by = auth()->user()->id;
        });

        static::updating(function (&$model) {
            $model->updated_by = auth()->user()->id;
        });
        static::deleting(function (&$model) {
            $model->delete_by = auth()->user()->id;
        });
    }


    


}






?>