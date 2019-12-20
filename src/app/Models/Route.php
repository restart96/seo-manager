<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table;

    protected $fillable = [
        'uri',
        'params',
        'mapping',
        'ord',
    ];

    protected $casts = [
        'params' => 'array',
        'mapping' => 'array',
    ];

    protected $hidden = [
        'ord',
        'created_at',
        'updated_at',
    ];

    /**
     * Route constructor.
     * 
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('seo-manager.database.routes_table');

        parent::__construct($attributes);
    }

    /**
     * Get SEO text data
     */
    public function translates()
    {
        return $this->hasMany(Translate::class, 'route_id', 'id');
    }
}
