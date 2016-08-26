<?php

if (!function_exists('arr_keyval_to_assoc')) {
    function arr_keyval_to_assoc($arr, $key_to_col, $val_to_col)
    {
        $ret = [];
        foreach ($arr as $k => $v) {
            $ret[] = [$key_to_col => $k, $val_to_col => $v];
        }

        return $ret;
    }
}
