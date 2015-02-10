<?php
/**
 * 过滤组件
 *
 * @author itsmikej
 */

namespace Moonrise\Component;

use Moonrise\Core\Registry;

class Filter
{
    static private $_array_type_map = array(
        MR_TYPE_ARRAY_INT	=>	MR_TYPE_INT,
        MR_TYPE_ARRAY_UINT	=>	MR_TYPE_UINT,
        MR_TYPE_ARRAY_NUM	=>	MR_TYPE_NUM,
        MR_TYPE_ARRAY_UNUM	=>	MR_TYPE_UNUM,
        MR_TYPE_ARRAY_STR	=>	MR_TYPE_STR,
        MR_TYPE_ARRAY_DATETIME	=>	MR_TYPE_DATETIME,
        MR_TYPE_ARRAY_BIGINT	=>	MR_TYPE_BIGINT,
    );

    public function __construct()
    {
    }

    public function filter(&$value, $var_type, $options=array('max'=>null, 'min'=>null, 'length'=>null, 'regex'=>null, 'default'=>null, 'default_key'=>null, 'pre_func'=>null, 'time_format'=>null, 'array'=>array(), 'enum_key'=>false, 'strict'=>false, 'xss_clean'=>false))
    {
        if (!isset($value)) {
            if (isset($options['default'])) {
                return $options['default'];
            }
            return null;
        }

        $var_type = isset($var_type) ? $var_type : MR_TYPE_DEFAULT;

        switch ($var_type) {
            case MR_TYPE_BOOL:
                $this->filterBool($value, $options);
                break;
            case MR_TYPE_STR:
                $this->filterStr($value, $options);
                break;
            case MR_TYPE_INT:
                $this->filterInt($value, $options);
                break;
            case MR_TYPE_UINT:
                $this->filterUInt($value, $options);
                break;
            case MR_TYPE_NUM:
                $this->filterNum($value, $options);
                break;
            case MR_TYPE_UNUM:
                $this->filterUNum($value, $options);
                break;
            case MR_TYPE_NOCLEAN:
                break;
            case MR_TYPE_BIGINT:
                $this->filterBigInt($value, $options);
                break;
            case MR_TYPE_DATETIME:
                $this->filterDataTime($value, $options);
                break;
            case MR_TYPE_JSON:
                break;
            case MR_TYPE_ENUM:
                $this->filterEnum($value, $options);
                break;
            case MR_TYPE_ENUM_KEYS:
                $this->filterEnumByKey($value, $options);
                break;
            case MR_TYPE_FILE:
                break;
            case MR_TYPE_ARRAY:
            case MR_TYPE_ARRAY_STR:
            case MR_TYPE_ARRAY_INT:
            case MR_TYPE_ARRAY_UINT:
            case MR_TYPE_ARRAY_NUM:
            case MR_TYPE_ARRAY_UNUM:
            case MR_TYPE_ARRAY_BIGINT:
            case MR_TYPE_ARRAY_DATETIME:
                if (!is_array($value) && isset($options['string']) && $options['string']) {
                    if (trim($value)) {
                        $separator = isset($options['separator']) ? $options['separator'] : ',';
                        $value = explode($separator, strval($value));
                    } else {
                        $value = array();
                    }
                } else {
                    $value = (array) $value;
                }
                $sub_var_type = isset(self::$_array_type_map[$var_type]) ? self::$_array_type_map[$var_type] : MR_TYPE_DEFAULT;

                foreach ($value as $tmp_key=>$tmp_value) {
                    $value[$tmp_key] = $this->filter($tmp_value, $sub_var_type, $options);
                }
                break;
            case MR_TYPE_DEFAULT:
            default:
                $this->filterStr($value, $options);
                break;
        }

        return $value;
    }

    protected function filterBool(&$value, $options)
    {
        $value = $value ? true : false;
    }

    protected function filterStr(&$value, $options)
    {
        $value = htmlspecialchars(trim($value));
        $this->processText($value, $options);
    }

    protected function filterInt(&$value, $options)
    {
        $value = (int)$value;
        $this->processNum($value, $options);
    }

    protected function filterUInt(&$value, $options)
    {
        $value = (int)$value;
        $this->processUNum($value, $options);
    }

    protected function filterNum(&$value, $options)
    {
        $value = (float)$value;
        $this->processNum($value, $options);
    }

    protected function filterUNum(&$value, $options)
    {
        $value = (float)$value;
        $this->processUNum($value, $options);
    }

    protected function filterBigInt(&$value, $options)
    {
        # int => string
        $value = int64val($value);
        $this->processNum($value, $options);
    }

    protected function filterDataTime(&$value, $options)
    {
        $this->processDateTime($value, $options);
    }

    /**
     * enum 数组
     * 检查数组，如果变量在提交的参数数组中，则返回
     * 否则，根据提交的数组和default参数，选择合适的返回值
     *
     * @param string & $value
     * @param array $options (pre_func=>callback, array=>enum array, default=>mixed, strict=>boolean)
     */
    protected function filterEnum(&$value, $options)
    {
        if (isset($options['pre_func']) && !is_null($options['pre_func']) && function_exists($options['pre_func'])) {
            #	$value = ${$options['pre_func']}($value);
            $value = call_user_func($options['pre_func'], $value);
        }

        if ($options['array'] == array()) {
            $value = isset($options['default']) ? $options['default'] : NULL;
        } else {
            $value = in_array($value, $options['array'], isset($options['strict']) && $options['strict']) ?
                $value : (isset($options['default']) ?
                    $options['default'] : current($options['array']));
        }
    }
    /**
     * 根据数组的key取enum元素
     * 检查数组，如果提交的数组中存在提交的key，则返回对应的value
     * 否则，根据提交的数组和default_key、default参数，选择合适的返回值
     *
     * @param mixed & $value
     * @param array $options (pre_func=>callback, array=>array, default=>mixed, default_key=>string/int, enum_key=>boolean)
     */
    protected function filterEnumByKey(&$value, $options)
    {
        if (isset($options['pre_func']) && is_callable($options['pre_func'])) {
            $value = call_user_func($options['pre_func'], $value);
        }
        if ($options['array'] == array()) {
            $value = isset($options['default']) ? $options['default'] : null;
        } else {
            $key = null;

            if (isset($options['array'][$value])) {
                # 如果有值，则记录为值
                $key   = $value;
                $value = $options['array'][$value];
            } else if (isset($options['default_key']) && !is_null($options['default_key'])) {
                # 如果没有值，则看是否有default_key
                $key   = $options['default_key'];
                $value = $options['array'][$options['default_key']];
            } else if (!is_null($options['default']) && in_array($options['default'], $options['array'])) {
                # 如果没有没有 default_key 则看有无 default 值，且 default 值应该在 options[array] 中
                $value = $options['default'];
            } else {
                # 如果情况都不符合，则使用 array 的第一个元素
                $value = current($options['array']);
            }
            # enum_key 为 true 则返回key，否则返回上述获取的value
            if (isset($options['enum_key']) && $options['enum_key']) {
                if ($key !== null) {
                    $value = $key;
                } else {
                    $value = array_search($value, $options['array']);
                }
            }
        }
    }

    /**
     * 处理文本字符
     * @param $value
     * @param $options
     */
    protected function processText(&$value, $options)
    {
        $value = str_replace(chr(0), '', $value);
        if (isset($options['length']) && $options['length']) {
            $value = substr($value, 0, $options['length']);
        }

        if (isset($options['regex']) && $options['regex']) {
            if (!preg_match($options['regex'], $value)) {
                $value = null;
            }
        }

        if (isset($options['xss_clean']) && $options['xss_clean']) {
            $security = Registry::getComponent('security');
            $value = $security->xss_clean($value);
        }
    }

    /**
     * 处理数值范围
     * @param $value
     * @param $options
     */
    protected function processNum(&$value, $options)
    {
        if (isset($options['min']) || $options['min']) {
            $ic = $value < $options['min'] ? 0 : 1;
        } else {
            $ic = 1;
        }

        if (isset($options['max']) || $options['max']) {
            if (isset($options['min'])) {
                $options['max'] = $options['max'] > $options['min'] ? $options['max'] : $options['min'];
            }
            $ac = $value > $options['max'] ? 0 : 1;
        } else {
            $ac = 1;
        }

        # 满足上限
        if ($ac) {
            # 不满足下限
            if (!$ic) {
                # default -> 下限 -> 上限 -> null
                if (isset($options['default'])) {
                    $value = $options['default'];
                } else if (isset($options['min'])) {
                    $value = $options['min'];
                } else if (isset($options['max'])) {
                    $value = $options['max'];
                } else {
                    $value = 0;
                }
            }
        } else {
            if ($ic) {
                # 满足下限
                # default -> 上限 -> 下限 -> null
                if (isset($options['default'])) {
                    $value = $options['default'];
                } else if (isset($options['max'])) {
                    $value = $options['max'];
                } else if (isset($options['min'])) {
                    $value = $options['min'];
                } else {
                    $value = 0;
                }
            } else {
                # 不满足下限
                # default -> 下限 -> 上限 -> null
                if (isset($options['default'])) {
                    $value = $options['default'];
                } else if (isset($options['min'])) {
                    $value = $options['min'];
                } else if (isset($options['max'])) {
                    $value = $options['max'];
                } else {
                    $value = 0;
                }
            }
        }
        return ;

        $value = $ac & $ic ?
            $value : (isset($options['default']) ?
                $options['default'] : (isset($options['min']) ?
                    $options['min'] : (isset($options['max']) ?
                        $options['max'] : 0
                    )
                )
            );
    }

    /**
     * 处理正数
     * @param $value
     * @param $options
     */
    protected function processUNum(&$value, $options)
    {
        if (!isset($options['min']) || empty($options['min']) || $options['min'] < 0) {
            $options['min'] = 0;
        }
        $this->processNum($value, $options);
    }

    /**
     * 处理时间
     * @param $value
     * @param $options
     */
    protected function processDateTime(&$value, $options)
    {
        if (isset($options['time_format'])) {
            $time_format = $options['time_format'];
        } else {
            $time_format = 'Y-m-d H:i:s';
        }
        if (!$time_format) {
            $value = null;
        } else {
            $value = date($time_format, strtotime((string)$value));
        }
    }

}