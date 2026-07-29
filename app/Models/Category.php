<?php

namespace App\Models;

use App\Models\Food;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToOrganisation;

class Category extends Model
{
    use HasFactory, BelongsToOrganisation;
    protected $table = 'categories';

    protected $fillable = [
        'organisation_id',
        'name',
        'description',
        'image',
    ];

    public function foods() {
        return $this->hasMany(Food::class);
    }
}
