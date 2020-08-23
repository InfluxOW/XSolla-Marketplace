<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use Sluggable;

    protected $fillable = ['name'];
    public $timestamps = false;
    protected $appends = ['games_count'];
    protected $casts = [
        'games_count' => 'integer'
    ];

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
     * Getters
     * */

    public function getGamesCountAttribute()
    {
        return $this->games->count();
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
