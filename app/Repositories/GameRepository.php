<?php

namespace App\Repositories;

use App\Distributor;
use App\Game;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class GameRepository
{
    public static function getGamesForIndexPage()
    {
        return QueryBuilder::for(Game::class)
            ->allowedFilters([
                AllowedFilter::exact('platform', 'platform.slug'),
                AllowedFilter::exact('distributor', 'distributors.slug'),
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
            ->with('availableKeys.distributor', 'platform')
            ->paginate(20)
            ->appends(request()->query());
    }
}
