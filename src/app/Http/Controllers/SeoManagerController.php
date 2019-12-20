<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Http\Controllers;

use App\Http\Controllers\Controller;

class SeoManagerController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('seo-manager::index');
    }
}
