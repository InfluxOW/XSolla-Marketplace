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
        $query =  QueryBuilder::for(Game::class)
            ->allowedFilters([
                AllowedFilter::exact('platform', 'platform.slug'),
                AllowedFilter::exact('distributor', 'distributors.slug'),
                AllowedFilter::scope('available'),
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
            ->with('availableKeys.distributor', 'platform');

        /*
         * Re-checking distributor filters because Spatie Query Builder is not working with
         * my distributorsWithAvailableKeys method and I couldn't build a query scope to replace it.
         * */

        return self::recheckFilters($query)
            ->paginate(20)
            ->appends(request()->query());
    }

    protected static function recheckFilters($query)
    {
        $distributorFilter = request()->query('filter')['distributor'] ?? null;

        if (isset($distributorFilter)) {
            $query = $query->get()->filter(function(Game $game) use ($distributorFilter) {
                return $game->distributorsWithAvailableKeys()->pluck('slug')->contains($distributorFilter);
            });
        }

        return $query;
    }
}
