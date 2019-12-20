<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Restart\SeoManager\App\Models\Language;
use Exception;

class LanguagesController extends Controller
{
    /**
     * 언어 목록을 반환합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $languages = $this->getLanguages();
            return response()->json(['languages' => $languages]);
        }
    }

    /**
     * 언어를 생성합니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $name = strtolower($request->input('name'));
            $language = Language::whereName($name)->first();

            if (!$language) {

                Language::create([
                    'name' => $name,
                ]);

                $languages = $this->getLanguages();
                return response()->json(['languages' => $languages]);
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
     * 언어 목록을 반환합니다.
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getLanguages()
    {
        return Language::get(['id', 'name']);
    }
}
