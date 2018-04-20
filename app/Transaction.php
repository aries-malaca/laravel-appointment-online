<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model{
    function items(){
        return $this->hasMany("App\TransactionItem");
    }
}
