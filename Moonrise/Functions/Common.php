<?php

if (!function_exists('remove_invisible_characters')) {
    function remove_invisible_characters($str, $url_encoded = true)
    {
        $non_displayables = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}

if (!function_exists('int64val')) {
    if (PHP_INT_SIZE >=8) {
        function int64val($int)
        {
            return intval($int);
        }
    } else {
        function int64val($int)
        {
            if (is_int($int)) {
                return $int;
            }
            $ret = '';
            $ret_len = 0;
            $strint = strval($int);
            $len = strlen($int);
            for ($i=0; $i<$len; $i++) {
                if ($ret_len && ($strint[$i] == ' ' || $strint[$i] == "\t" || $strint[$i] == "\r" || $strint[$i] == "\n")) {
                    continue;
                }
                if ($strint[$i] >= '0' && $strint[$i] <= '9') {
                    $ret .= $strint[$i];
                    ++$ret_len;
                } else {
                    break;
                }
            }
            if (empty($ret_len)) {
                return 0;
            } else {
                return $ret;
            }
        }
    }
}



if (!function_exists('show_error')) {
    function show_error($message)
    {
        # todo 界面友好
        die($message);
    }
}