<?php

if (!function_exists('drupal_get_path')) {
    function drupal_get_path($type, $name)
    {
        switch ($type) {
            case 'module':
                return 'modules/'.$name;
        }

        throw new \LogicException(
            sprintf('Type "%s" not implemented by this polyfill method', $type)
        );
    }
}
