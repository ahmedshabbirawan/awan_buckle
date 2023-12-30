<?php 
namespace App\Traits;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

use Attribute;

trait Definitions{

  static $STATUS_INACTIVE = 0;
  static $STATUS_ACTIVE = 1;
  
  static $STATUS_INACTIVE_STRING = 'In Active';
  static $STATUS_ACTIVE_STRING = 'Active';



//   static $DESIGNATION_ = 'Active';



  static $ATTRIBUTE_RAM         = 'RAM';
  static $ATTRIBUTE_RAM_STRING  = 'RAM v';

  static $ATTRIBUTE_PROCCESSOR          = 'PROCCESSOR';
  static $ATTRIBUTE_PROCCESSOR_STRING   = 'PROCCESSOR v';


  static $ATTRIBUTE_HARDDISK          = 'HARD_DISK';
  static $ATTRIBUTE_HARDDISK_STRING   = 'HARD DISK';


  static $ATTRIBUTE_UOM          = 'UOM';
  static $ATTRIBUTE_UOM_STRING   = 'Unit of Measure';


  static $CONDITION_WORKING       = '1';
  static $CONDITION_DEAD          = '2'; 
  static $CONDITION_DAMAGED       = '3';  
  static $CONDITION_FAULTY        = '4';
  
  static $CONDITION_WORKING_STR       = 'Working';
  static $CONDITION_DEAD_STR          = 'Dead'; 
  static $CONDITION_DAMAGED_STR       = 'Damaged';  
  static $CONDITION_FAULTY_STR        = 'Faulty';



  static function statusLabel($int){

    if($int == self::$STATUS_INACTIVE){
      return '<span class="label label-sm label-danger">'.self::$STATUS_INACTIVE_STRING.'</span>';
    }elseif($int == self::$STATUS_ACTIVE){
      return '<span class="label label-sm label-success">'.self::$STATUS_ACTIVE_STRING.'</span>';
    }else{
      return 'Other';
    }
    
  }


  static function conditionLabel($int){

    if($int == self::$CONDITION_WORKING){
      return '<span class="label label-sm label-success">'.self::$CONDITION_WORKING_STR.'</span>';
    }elseif($int == self::$CONDITION_DEAD){
      return '<span class="label label-sm label-danger">'.self::$CONDITION_DEAD_STR.'</span>';
    }elseif($int == self::$CONDITION_FAULTY){
      return '<span class="label label-sm label-danger">'.self::$CONDITION_FAULTY_STR.'</span>';
    }elseif($int == self::$CONDITION_DAMAGED){
      return '<span class="label label-sm label-danger">'.self::$CONDITION_DAMAGED_STR.'</span>';
    }else{
      return 'N/A';
    }
    
  }


  function getUserNameByID($id){
    $user = User::find($id);
    $roles = $user->getRoleNames();

    return $user->userName.' - '.$roles[0];

  }


  
  



}






?>