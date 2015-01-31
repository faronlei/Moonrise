<?php
/**
 * 过滤组件
 *
 * @author itsmikej
 */

namespace Moonrise\Component;

class Filter
{
    public function filter($value, $var_type, $options=array())
    {
        return func_get_args();
    }
}