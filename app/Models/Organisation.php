<?php

namespace App\Models;

use App\Models\User;
use App\Models\Food;
use App\Models\Category;
use App\Models\Table;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'seats_limit',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
