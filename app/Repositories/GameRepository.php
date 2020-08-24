<?php

namespace App\Repositories;

use App\Game;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class GameRepository
{
    public static function getGamesForIndexPage()
    {
        return  QueryBuilder::for(Game::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('platform', 'platform.slug'),
                AllowedFilter::exact('distributor', 'distributors.slug'),
                AllowedFilter::scope('available'),
                AllowedFilter::scope('available_at', 'available_at_distributor'),
                AllowedFilter::callback('price_lte', function (Builder $query, $price) {
                    return $query->where('price', '<=', $price);
                }),
                AllowedFilter::callback('price_gte', function (Builder $query, $price) {
                    return $query->where('price', '>=', $price);
                }),
            ])
            ->allowedSorts([
                AllowedSort::field('platform', 'platform_id'),
                AllowedSort::field('price'),
                AllowedSort::field('name'),
            ])
            ->latest('updated_at')
            ->with('keys.distributor', 'keys.purchase', 'platform')
            ->paginate(20)
            ->appends(request()->query());
    }
}
