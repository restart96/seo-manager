<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Restart\SeoManager\App\Models\Locale;
use Exception;

class LocalesController extends Controller
{
    /**
     * 지역 목록을 반환합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $locales = $this->getLocales();
            return response()->json(['locales' => $locales]);
        }
    }

    /**
     * 지역을 생성합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $name = strtolower($request->input('name'));
            $locale = Locale::whereName($name)->first();

            if (!$locale) {

                Locale::create([
                    'name' => $name,
                ]);

                $locales = $this->getLocales();
                return response()->json(['locales' => $locales]);
            }

            throw new Exception('지역이 이미 존재합니다');

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 지역 목록을 반환합니다.
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getLocales()
    {
        return Locale::get('name');
    }
}
