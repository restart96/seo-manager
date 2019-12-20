<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $table;

    protected $fillable = [
        'name',
    ];

    /**
     * Locale constructor.
     * 
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('seo-manager.database.locales_table');

        parent::__construct($attributes);
    }
}
