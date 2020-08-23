<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use Sluggable;

    protected $fillable = ['name'];
    public $timestamps = false;

    /*
     * Relations
     * */

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function distributors()
    {
        return $this->hasMany(Distributor::class);
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
