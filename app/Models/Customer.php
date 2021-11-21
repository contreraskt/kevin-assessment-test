<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'last_name', 'gender', 'date_of_birth','contact_number','email'];

    public function purchases()
    {
        return $this->hasMany(PurchaseTransaction::class);
    }


}
