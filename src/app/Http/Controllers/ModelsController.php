<?php
/**
 * @author SJ
 * @date   2019.12.09
 */

namespace Restart\SeoManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ModelsController extends Controller
{
    /**
     * 예외처리할 Column 목록
     * 
     * @var array
     */
    protected $exceptColumns = [
        'password',
        'remember_token',
    ];

    /**
     * 모델 목록을 조회합니다.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModels()
    {
        $models = $this->getModelsFromFile();

        $modelColumns = collect();
        foreach ($models as $model) {
            $columns = $this->getColumnsOfModel($model);
            $modelColumns->push([
                'model' => $model,
                'columns' => $columns,
            ]);
        }

        return response()->json([
            'models' => $modelColumns,
        ]);
    }

    /**
     * 모델 목록을 조회합니다.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getModelsFromFile()
    {
        $path = base_path('app').'/'.config('seo-manager.models_path');
        $files = File::allFiles($path);

        $models = collect();
        foreach ($files as $file) {
            foreach (file($file) as $line) {
                if (preg_match('/^namespace (.+);/i', trim($line), $match)) {
                    $namespace = '\\'.$match[1].'\\'.pathinfo($file, PATHINFO_FILENAME);
                    $models->push($namespace);
                }
            }
        }

        return $models;
    }

    /**
     * 각 모델별 컬럼 목록을 반환합니다.
     * 
     * @param  string  $model
     * @return array
     */
    private function getColumnsOfModel($model)
    {
        $model = new $model;

        $appends = [];
        if (method_exists($model, 'getAppends')) {
            $appends = $model->getAppends();
        }

        $table = $model->getTable();
        $columns = $model->getConnection()
                         ->getSchemaBuilder()
                         ->getColumnListing($table);

        $excepts = array_merge($this->exceptColumns, config('seo-manager.except_columns'));

        return array_merge(array_diff($columns, $excepts), $appends);
    }
}
