<?php
/**
 * 过滤组件
 *
 * @author itsmikej
 */

namespace Moonrise\Component;

use Moonrise\Core\Loader;
use Moonrise\Core\Registry;

class Filter
{
    public function __construct()
    {
    }

    public function filter(&$value, $var_type, $options=array('max'=>null, 'min'=>null, 'length'=>null, 'regex'=>null, 'default'=>null, 'default_key'=>null, 'pre_func'=>null, 'array'=>array(), 'enum_key'=>false, 'strict'=>false, 'xss_clean'=>false))
    {
        if (!isset($value)) {
            if (isset($options['default'])) {
                return $options['default'];
            }
            return null;
        }

        if (isset($options['pre_func']) && function_exists($options['pre_func'])) {
            $value = call_user_func($options['pre_func'], $value);
        }

        if (isset($options['xss_clean']) && $options['xss_clean']) {
            $securityObj = Registry::getComponent('security');

        }

        switch ($var_type) {
            case MR_STR:
                $this->filterStr($value, $options);
                break;
            case MR_INT:
                break;

            case MR_DEFAULT:
            default:
                $this->filterStr($value, $options);
                break;
        }

        return $value;
    }

    protected function filterStr(&$value, $options)
    {

    }
}