<?php
/**
 * @author SJ
 * @date   2019.12.18
 */

namespace Restart\SeoManager\App;

use Restart\SeoManager\App\Models\Route as RouteModel;
use Route;

class SeoManager
{
    private $route = null;

    protected static $data = null;

    /**
     * 데이터를 반환합니다.
     * 
     * @param  string  $property
     * @return array|string
     */
    public function getData($property = null)
    {
        if (static::$data === null) {
            $this->setAllData();
        }

        if ($property) {
            return isset(static::$data[$property]) ? static::$data[$property] : null;
        }

        return static::$data;
    }

    /**
     * 모든 메타데이터를 조회합니다.
     * 
     * @return array
     */
    private function setAllData()
    {
        $route = Route::current();
        $params = $route->parameters;

        if (!$this->route) {
            $uri = $route->uri();
            $this->route = RouteModel::where('uri', $uri)->first();
        }

        $data = [];

        if ($this->route) {
            $translate = $this->route->translates()
                                     ->locale(config('app.locale'))
                                     ->first();
            if ($translate) {
                $params = $this->getMappedParams($params, $this->route->mapping);

                if ($translate->title && count($translate->title)) {
                    $data['title'] = trim($this->getMappedData($translate->title, $params, ' '));
                }
                if ($translate->meta_title && count($translate->meta_title)) {
                    $data['meta_title'] = trim($this->getMappedData($translate->meta_title, $params, ' '));
                }
                if ($translate->meta_description) {
                    if ($translate->meta_description['mapped']) {
                        $data['meta_description'] = $this->getMappedData($translate->meta_description['value'], $params, ' ');
                    } else {
                        $data['meta_description'] = trim(preg_replace('/\s+/', ' ', $translate->meta_description['value']));
                    }
                }
                if ($translate->meta_keywords) {
                    if ($translate->meta_keywords['mapped']) {
                        $data['meta_keywords'] = $this->getMappedData($translate->meta_keywords['value'], $params, ',');
                    } else {
                        $data['meta_keywords'] = trim(implode(',', $translate->meta_keywords['value']));
                    }
                }
                if ($translate->meta_image) {
                    if ($translate->meta_image['mapped']) {
                        $data['meta_image'] = $this->getMappedData($translate->meta_image['value'], $params);
                    } else {
                        $data['meta_image'] = trim($translate->meta_image['value']);
                    }
                }
                if ($translate->meta_url) {
                    if ($translate->meta_url['mapped']) {
                        $data['meta_url'] = $this->getMappedData($translate->meta_url['value'], $params);
                    } else if ($translate->meta_url['value']) {
                        $data['meta_url'] = trim($translate->meta_url['value']);
                    } else {
                        $data['meta_url'] = url()->full();
                    }
                }
            }
        }

        static::$data = $data;
    }

    /**
     * Title tag 문자열을 반환합니다.
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Meta tag title 문자열을 반환합니다.
     * 
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getData('meta_title');
    }

    /**
     * Meta tag description 문자열을 반환합니다.
     * 
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getData('meta_description');
    }

    /**
     * Meta tag keywords 문자열을 반환합니다.
     * 
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getData('meta_keywords');
    }

    /**
     * Meta tag image 문자열을 반환합니다.
     * 
     * @return string
     */
    public function getMetaImage()
    {
        return $this->getData('meta_image');
    }

    /**
     * Meta tag URL 문자열을 반환합니다.
     * 
     * @return string
     */
    public function getMetaUrl()
    {
        return $this->getData('meta_url');
    }

    /**
     * 파라미터에 리소스를 매핑합니다.
     * 
     * @param  array  $params
     * @param  array  $mapping
     * @return array
     */
    private function getMappedParams($params, $mapping)
    {
        foreach ($params as $param => &$val) {
            if (!isset($mapping[$param])) {
                continue;
            }

            $mapped = $mapping[$param];

            if (gettype($val) === 'object') {
                $val = $val->only($mapped['columns']);
            } else {
                $model = (new $mapped['model'])->where($mapped['find_by'], $val)
                                               ->first();
                $val = $model ? $model->only($mapped['columns']) : '';
            }
        }
        unset($val);

        return $params;
    }

    /**
     * 데이터에 리소스를 매핑합니다.
     * 
     * @param  array|string  $data
     * @param  array  $params
     * @return string
     */
    private function getMappedData($data, $params, $glue = ' ')
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        foreach ($data as &$val) {
            if (preg_match('/{([^-}]+)-([^-}]+)}/', $val, $match)) {
                if (isset($params[strtolower($match[1])])) {
                    $param = $params[strtolower($match[1])];
                    if (isset($param[strtolower($match[2])])) {
                        $val = $param[strtolower($match[2])];
                    }
                }
            }
        }
        unset($val);

        return implode($glue, $data);
    }
}
