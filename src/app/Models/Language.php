<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
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
        $this->table = config('seo-manager.database.languages_table');

        parent::__construct($attributes);
    }
}
