<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Branch extends Model implements Auditable{
    use \OwenIt\Auditing\Auditable;

    protected $auditExclude = [
        'created_at','updated_at'
    ];
}