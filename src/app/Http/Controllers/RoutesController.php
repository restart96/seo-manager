<?php
/**
 * @author SJ
 * @date   2019.12.05
 */

namespace Restart\SeoManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Restart\SeoManager\App\Models\Route;
use Exception;

class RoutesController extends Controller
{
    /**
     * 예외처리할 Route 목록
     * 
     * @var array
     */
    protected $exceptRoutes = [
        'api',
        'telescope',
        '_debugbar',
    ];

    /**
     * Route 목록을 조회합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $locale = $request->input('locale');
        $routes = $this->getRoutes($locale);

        return response()->json([
            'routes' => $routes,
        ]);
    }

    /**
     * Route를 생성 및 업데이트합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $ord = Route::max('ord') + 1;

        $routes = $this->getRoutesFromFile();
        foreach ($routes as $uri) {
            if (!Route::where('uri', $uri)->count()) {
                Route::create([
                    'uri' => $uri,
                    'params' => $this->getParamsFromURI($uri),
                    'ord' => $ord++,
                ]);
            }
        }

        $locale = $request->input('locale');
        $routes = $this->getRoutes($locale);

        return response()->json([
            'routes' => $routes,
        ], 201);
    }

    /**
     * Route를 편집합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, int $id)
    {
        $locale = $request->input('locale');
        $route = $this->getRoute($id, $locale);

        return response()->json([
            'route' => $route,
        ]);
    }

    /**
     * Route를 업데이트합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {

            $route = Route::find($id);
            if (!$route) {
                throw new Exception('Route정보를 찾을 수 없습니다.');
            }

            // Mapping 업데이트
            $mapping = json_decode($request->input('mapping'), true);
            $route->update([
                'mapping' => $mapping,
            ]);

            // 데이터 업데이트
            $locale = $request->input('locale');
            $translate = json_decode($request->input('translate'), true);

            // 파일 업로드
            $meta_image_file = $request->file('meta_image_file');
            if ($meta_image_file) {
                $translate['meta_image']['value'] = $this->uploadImage($meta_image_file);
            }

            $translateModel = $route->translates()->locale($locale);
            if ($translateModel->count()) {
                $translateModel->first()->update($translate);
            } else {
                $translate['locale'] = $locale;
                $translateModel->create($translate);
            }

            return response()->json([
                'updated' => true,
                'route' => $this->getRoute($id, $locale),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Route를 삭제합니다.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {

            $route = Route::find($id);
            if (!$route) {
                throw new Exception('Route정보를 찾을 수 없습니다.');
            }

            $route->translates()->delete();
            $route->delete();

            return response()->json(['deleted' => true]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Route 정렬순서를 업데이트합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reOrder(Request $request)
    {
        try {

            $items = $request->input('items');
            foreach ($items as $index => $item) {
                Route::where('id', $item['id'])->update([
                    'ord' => $index + 1,
                ]);
            }

            return response()->json(['updated' => true]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Route 목록을 조회합니다.
     * 
     * @param  string  $locale
     * @return array
     */
    private function getRoutes($locale)
    {
        $routes = Route::with(['translates' => function($query) use ($locale) {
            $query->locale($locale);
        }])->orderBy('ord', 'asc')->get()->toArray();

        foreach ($routes as &$route) {
            if (isset($route['translates'])) {
                $route['translate'] = count($route['translates']) ? $route['translates'][0] : (object)[];
                unset($route['translates']);
            }
            $route['default_url'] = $this->getDefaultUrl($route['uri'], $route['mapping']);
        }
        unset($route);

        return $routes;
    }

    /**
     * Route를 조회합니다.
     * 
     * @param  int  $id
     * @param  string  $locale
     * @return array
     */
    private function getRoute($id, $locale)
    {
        $route = Route::with(['translates' => function($query) use ($locale) {
            $query->locale($locale);
        }])->find($id)->toArray();

        if (isset($route['translates'])) {
            $route['translate'] = count($route['translates']) ? $route['translates'][0] : (object)[];
            unset($route['translates']);
        }
        $route['default_url'] = $this->getDefaultUrl($route['uri'], $route['mapping']);

        return $route;
    }

    /**
     * Route file에 설정된 목록을 반환합니다.
     * 
     * @return array
     */
    private function getRoutesFromFile()
    {
        $routes = \Route::getRoutes();
        $routes = array_keys($routes->get('GET'));
        $excepts = array_merge($this->exceptRoutes, config('seo-manager.except_routes'), [config('seo-manager.route')]);

        foreach ($routes as $key => $route) {
            foreach ($excepts as $uri) {
                $exist = strpos($route, $uri);
                if ($exist !== false && $exist === 0) {
                    unset($routes[$key]);
                }
            }
        }

        return $routes;
    }

    /**
     * URI에서 파라미터를 추출합니다.
     * 
     * @param  string  $uri
     * @return mixed
     */
    private function getParamsFromURI($uri)
    {
        preg_match_all('/{(.*?)}/', $uri, $match);

        return count($match) > 1 ? $match[1] : [];
    }

    /**
     * 기본 URL을 생성하여 반환합니다.
     */
    private function getDefaultUrl($uri, $mapping)
    {
        
        if (is_array($mapping)) {
            foreach ($mapping as $param => $data) {
                if (!$data['model']) {
                    return null;
                }
                $model = (new $data['model'])->first();
                if (!$model) {
                    return null;
                }

                $uri = preg_replace('/{'.$param.'}/', $model->getKey(), $uri);
            }
        }

        if (!preg_match('/^http(s?)\:\/\//i', $uri)) {
            $uri = '/'.preg_replace('/^\/(.*)$/', '$1', $uri);
        }

        return $uri;
    }

    /**
     * 이미지를 업로드합니다.
     * 
     * @param  Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private function uploadImage($file)
    {
        $name = preg_replace('/ /', '_', $file->getClientOriginalName());
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $hash = date('YmdHi_').md5(time().$name);

        $path = config('seo-manager.filesystems.path');
        $storage = Storage::disk(config('seo-manager.filesystems.disk'));
        $result = $storage->putFileAs($path, $file, $hash.'.'.$ext);

        return $storage->url($result);
    }
}
