<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

trait OptimizedQueries
{
    /**
     * Mendapatkan data dengan cache
     *
     * @param string $key
     * @param int $minutes
     * @param \Closure $callback
     * @return mixed
     */
    public function getCached(string $key, int $minutes, \Closure $callback)
    {
        return Cache::remember($key, $minutes * 60, $callback);
    }
    
    /**
     * Mendapatkan data dengan eager loading relasi
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRelations(Builder $query, array $relations = []): Builder
    {
        return $query->with($relations);
    }
    
    /**
     * Mendapatkan data dengan count relasi
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCount(Builder $query, array $relations = []): Builder
    {
        return $query->withCount($relations);
    }
    
    /**
     * Mendapatkan data dengan filter status aktif
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Mendapatkan data dengan filter status publikasi
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
    
    /**
     * Mendapatkan data dengan filter pencarian
     *
     * @param string $keyword
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $keyword, array $fields = ['name']): Builder
    {
        $query->where(function ($query) use ($keyword, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$keyword}%");
            }
        });
        
        return $query;
    }
} 