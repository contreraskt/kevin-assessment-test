<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model{

    protected $fillable = ['customer_id', 'voucher_code', 'status','locked_at'];
    use HasFactory;


}
