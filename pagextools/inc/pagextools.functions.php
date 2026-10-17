<?php

/**
 * Page XTools - library functions
 *
 * Plugin for Cotonti v.1+, PHP 8.5+
 * Filename: plugins/pagextools/inc/pagextools.functions.php
 * Purpose: library functions
 * Date: Oct 17Th, 2026
 * Note: 
 * Source: https://github.com/webitproff/pagextools-cotonti
 * WepPage: https://abuyfile.com/ru/market/cotonti/plugs/pagextools
 * Support: https://abuyfile.com/ru/forums/cotonti/custom/plugs
 *
 * @package pagextools
 * @version 1.0.0
 * @author webitproff
 * @copyright (c) 2026 webitproff | https://github.com/webitproff 
 * @license BSD-3-Clause
 */
 
 
defined('COT_CODE') or die('Wrong URL.');

/**
 * Renders a category <select> with data-depth attributes (Select2-friendly).
 *
 * @param string $extension Extension code
 * @param string $check     Selected value
 * @param string $name      Dropdown name
 * @param string $subcat    Show only subcats of the selected category
 * @param bool   $hidePrivate Hide private categories
 * @param bool   $isModule  TRUE for modules, FALSE for plugins
 * @param bool   $addEmpty  Allow empty choice
 * @param mixed  $attrs     Additional attributes as an associative array or a string
 * @param string $customRC  Custom resource string name
 * @return string
 */
function cot_pagextools_selectbox_structure_select2(
    $extension,
    $check,
    $name,
    $subcat = '',
    $hidePrivate = true,
    $isModule = true,
    $addEmpty = false,
    $attrs = '',
    $customRC = ''
) {
    $categories = is_array(Cot::$structure[$extension]) ? Cot::$structure[$extension] : [];

    $options = [];

    foreach ($categories as $code => $category) {
        $display = ($hidePrivate && $isModule) ? cot_auth($extension, $code, 'W') : true;

        if ($display && !empty($subcat) && isset(Cot::$structure[$extension][$subcat])) {
            $mtch    = Cot::$structure[$extension][$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($category['path'], 0, $mtchlen) == $mtch || $code === $subcat);
        }

        if ((!$isModule || cot_auth($extension, $code, 'R')) && $code !== 'all' && $display) {
            $depth     = substr_count($category['path'], '.');
            $selected  = ($code === $check) ? ' selected' : '';
            $attrs_str = is_array($attrs) ? cot_rc_attr_string($attrs) : $attrs;

            $options[] = '<option value="' . htmlspecialchars($code) . '" data-depth="' . $depth . '"' . $selected . ' ' . $attrs_str . '>' .
                         htmlspecialchars($category['title']) . '</option>';
        }
    }

    if ($addEmpty) {
        array_unshift($options, '<option value="">---</option>');
    }

    foreach (cot_getextplugins('pagextools.selectBox.structure') as $pl) {
        include $pl;
    }

    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . implode("\n", $options) . '</select>';
}

/**
 * Category <select> for the admin search form (Select2-friendly).
 *
 * @param string $check       Selected category code
 * @param string $name        Name of the select input
 * @param string $subcat      Parent category code for filtering subcategories
 * @param bool   $hideprivate Hide private categories
 * @return string
 */
function cot_pagextools_selectcat_select2($check, $name, $subcat = '', $hideprivate = true)
{
    $structurePage = is_array(Cot::$structure['page']) ? Cot::$structure['page'] : [];

    $options = '';

    foreach ($structurePage as $i => $x) {
        $display = $hideprivate ? cot_auth('page', $i, 'R') : true;

        if ($display && !empty($subcat) && isset($structurePage[$subcat])) {
            $mtch    = $structurePage[$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if (cot_auth('page', $i, 'R') && $i !== 'all' && $display) {
            $depth    = substr_count($x['path'], '.');
            $selected = ($i == $check) ? ' selected' : '';

            $options .= '<option value="' . htmlspecialchars($i) . '" data-depth="' . $depth . '"' . $selected . '>' .
                        htmlspecialchars($x['title']) . '</option>';
        }
    }

    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . $options . '</select>';
}

/**
 * Plain category <select> for the admin search form (fallback, no Select2).
 *
 * @param string $check       Selected category code
 * @param string $name        Name of the select input
 * @param string $subcat      Parent category code for filtering subcategories
 * @param bool   $hideprivate Hide private categories
 * @return string
 */
function cot_pagextools_selectcat($check, $name, $subcat = '', $hideprivate = true)
{
    $structurePage = is_array(Cot::$structure['page']) ? Cot::$structure['page'] : [];

    $result_array = [];
    foreach ($structurePage as $i => $x) {
        $display = $hideprivate ? cot_auth('page', $i, 'R') : true;

        if ($display && !empty($subcat) && isset($structurePage[$subcat])) {
            $mtch    = $structurePage[$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if (cot_auth('page', $i, 'R') && $i != 'all' && $display) {
            $result_array[$i] = $x['tpath'];
        }
    }

    return cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), true);
}