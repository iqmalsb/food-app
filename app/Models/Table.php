<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToOrganisation;

class Table extends Model
{
    use HasFactory, BelongsToOrganisation;
    protected $table = 'tables';

    protected $fillable = [
        'organisation_id',
        'table_no',
        'max_pax',
        'status',
    ];

    public function orders() {
        return $this->belongsToMany(Order::class);
    }
}
