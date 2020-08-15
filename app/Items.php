<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Items extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'items';
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = ['name' , 'email' , 'phone' , 'address' , 'zip'];

    public function images()
    {
        return $this->hasMany(ItemImages::class, 'item_id');
    }
}
