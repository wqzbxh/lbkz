<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permission';

    public function getInfo()
    {
        $Result = self::all();
        if($Result->isNotEmpty()){
            $Result =  $Result->toArray();
        }

        return $Result;
    }
}
