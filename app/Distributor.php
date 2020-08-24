<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use Sluggable;

    protected $fillable = ['name'];
    public $timestamps = false;

    /*
     * Relations
     * */

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, Key::class, 'distributor_id', 'id', 'id', 'game_id')->distinct('name');
    }

    /*
     * Other
     * */

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
