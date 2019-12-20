<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Models;

use Illuminate\Database\Eloquent\Model;

class Translate extends Model
{
    protected $table;

    protected $fillable = [
        'route_id',
        'locale',
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_url',
        'meta_image',
    ];

    protected $casts = [
        'title' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
        'meta_url' => 'array',
        'meta_image' => 'array',
    ];

    protected $hidden = [
        'id',
        'route_id',
        'locale',
        'created_at',
        'updated_at',
    ];

    /**
     * Translate constructor.
     * 
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('seo-manager.database.translates_table');

        parent::__construct($attributes);
    }

    /**
     * Set locale
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }
}
