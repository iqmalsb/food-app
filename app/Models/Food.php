<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToOrganisation;

class Food extends Model
{
    use HasFactory, BelongsToOrganisation;
    protected $table = 'food';

    protected $fillable = [
        'organisation_id',
        'name',
        'description',
        'image',
        'price',
        'category_id',
    ];

    public function categories() {
        return $this->belongsTo(Category::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class);
    }
}
