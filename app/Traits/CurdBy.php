<?php 
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

use Attribute;



  trait CurdBy{

    protected static function curdBy() {
        parent::boot();

        static::creating(function (&$model) {
            if (empty($model->created_by)) {
                $model->created_by = auth()->check() ? auth()->user()->id : 1;
            }
        });

        static::updating(function (&$model) {
            if (empty($model->updated_by)) {
                $model->updated_by = auth()->check() ? auth()->user()->id : 1;
            }
        });
        static::deleting(function (&$model) {
            if (empty($model->delete_by)) {
                $model->delete_by = auth()->check() ? auth()->user()->id : 1;
            }
        });
    }


    


}






?>