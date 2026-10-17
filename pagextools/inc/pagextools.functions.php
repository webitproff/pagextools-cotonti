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
 * Category titles are translated via the core "i18n" plugin when the current
 * UI locale differs from the default site language. If the i18n plugin is not
 * active or the translation is missing, the original structure title is used.
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
    // Global state of the core "i18n" plugin: current locale and read flag
    global $i18n_read, $i18n_locale;

    $categories = is_array(Cot::$structure[$extension]) ? Cot::$structure[$extension] : [];

    $options = [];

    // i18n is considered active when the plugin is enabled for reading AND
    // the current UI locale is set AND it differs from the default site language.
    // In that case category titles are looked up in the i18n dictionary.
    $i18n_enabled = $i18n_read
        && !empty($i18n_locale)
        && $i18n_locale != Cot::$cfg['defaultlang'];

    foreach ($categories as $code => $category) {
        $display = ($hidePrivate && $isModule) ? cot_auth($extension, $code, 'W') : true;

        if ($display && !empty($subcat) && isset(Cot::$structure[$extension][$subcat])) {
            $mtch    = Cot::$structure[$extension][$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($category['path'], 0, $mtchlen) == $mtch || $code === $subcat);
        }

        if ((!$isModule || cot_auth($extension, $code, 'R')) && $code !== 'all' && $display) {
            // Default title from the structure registry
            $title = $category['title'];

            // Replace with the translated title when i18n is active
            if (cot_plugin_active('i18n') && $i18n_enabled) {
                $translated_cat = cot_i18n_get_cat($code, $i18n_locale);
                if ($translated_cat && !empty($translated_cat['title'])) {
                    $title = $translated_cat['title'];
                }
            }

            $depth     = substr_count($category['path'], '.');
            $selected  = ($code === $check) ? ' selected' : '';
            $attrs_str = is_array($attrs) ? cot_rc_attr_string($attrs) : $attrs;

            $options[] = '<option value="' . htmlspecialchars($code) . '" data-depth="' . $depth . '"' . $selected . ' ' . $attrs_str . '>' .
                         htmlspecialchars($title) . '</option>';
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
 * Behaves like cot_market_selectcat_select2():
 * - always emits an empty "All categories" option as the first item;
 * - this empty option is marked "selected" when $check is empty,
 *   so the browser shows "All categories" instead of the first real one;
 * - a specific category is highlighted only if $check matches its code.
 *
 * Category titles are translated via the core "i18n" plugin when the current
 * UI locale differs from the default site language.
 *
 * @param string $check       Selected category code
 * @param string $name        Name of the select input
 * @param string $subcat      Parent category code for filtering subcategories
 * @param bool   $hideprivate Hide private categories
 * @return string
 */
function cot_pagextools_selectcat_select2($check, $name, $subcat = '', $hideprivate = true)
{
    // Global state of the core "i18n" plugin: current locale and read flag
    global $i18n_read, $i18n_locale;

    $structurePage = is_array(Cot::$structure['page']) ? Cot::$structure['page'] : [];

    // i18n is active when the plugin is enabled for reading AND the current UI
    // locale is set AND it differs from the default site language.
    $i18n_enabled = $i18n_read
        && !empty($i18n_locale)
        && $i18n_locale != Cot::$cfg['defaultlang'];

    // Label for the empty option — taken from the plugin's language file
    $emptyLabel = Cot::$L['pagextools_all_categories'] ?? 'All categories';

    // Empty option FIRST, marked "selected" when no category is chosen.
    // This guarantees that with ?c= absent the browser highlights "All categories"
    // instead of picking the first real category on its own.
    $options = '<option value=""'
        . (empty($check) ? ' selected' : '')
        . '>'
        . htmlspecialchars($emptyLabel)
        . '</option>';

    foreach ($structurePage as $i => $x) {
        $display = $hideprivate ? cot_auth('page', $i, 'R') : true;

        if ($display && !empty($subcat) && isset($structurePage[$subcat])) {
            $mtch    = $structurePage[$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if (cot_auth('page', $i, 'R') && $i !== 'all' && $display) {
            // Default title from the structure registry
            $title = $x['title'];

            // Replace with the translated title when i18n is active
            if (cot_plugin_active('i18n') && $i18n_enabled) {
                $translated_cat = cot_i18n_get_cat($i, $i18n_locale);
                if ($translated_cat && !empty($translated_cat['title'])) {
                    $title = $translated_cat['title'];
                }
            }

            $depth = substr_count($x['path'], '.');

            // "selected" only for a real match; empty $check never matches here,
            // because $i is always a non-empty category code
            $selected = (!empty($check) && $i == $check) ? ' selected' : '';

            $options .= '<option value="' . htmlspecialchars($i) . '" data-depth="' . $depth . '"' . $selected . '>' .
                        htmlspecialchars($title) . '</option>';
        }
    }

    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . $options . '</select>';
}

/**
 * Plain category <select> for the admin search form (fallback, no Select2).
 *
 * Cotonti's pre-built "$x['tpath']" is used as the option label. When the core
 * "i18n" plugin is active and the UI locale differs from the default site
 * language, the tpath is rebuilt from translated titles using the site
 * separator (Cot::$cfg['separator'], default " > ").
 *
 * @param string $check       Selected category code
 * @param string $name        Name of the select input
 * @param string $subcat      Parent category code for filtering subcategories
 * @param bool   $hideprivate Hide private categories
 * @return string
 */
function cot_pagextools_selectcat($check, $name, $subcat = '', $hideprivate = true)
{
    // Global state of the core "i18n" plugin: current locale and read flag
    global $i18n_read, $i18n_locale;

    $structurePage = is_array(Cot::$structure['page']) ? Cot::$structure['page'] : [];

    // i18n is active when the plugin is enabled for reading AND the current UI
    // locale is set AND it differs from the default site language.
    $i18n_enabled = $i18n_read
        && !empty($i18n_locale)
        && $i18n_locale != Cot::$cfg['defaultlang'];

    // Path separator used by Cotonti's tpath builder (" > " by default)
    $tpath_separator = Cot::$cfg['separator'] ?? ' > ';

    $result_array = [];
    foreach ($structurePage as $i => $x) {
        $display = $hideprivate ? cot_auth('page', $i, 'R') : true;

        if ($display && !empty($subcat) && isset($structurePage[$subcat])) {
            $mtch    = $structurePage[$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if (cot_auth('page', $i, 'R') && $i != 'all' && $display) {
            // Default: use Cotonti's pre-built tpath as-is
            $tpath = $x['tpath'];

            // When i18n is active — rebuild tpath from translated category titles
            if (cot_plugin_active('i18n') && $i18n_enabled) {
                $titleParts = [];
                foreach (explode('.', $x['path']) as $part) {
                    $partTitle = $structurePage[$part]['title'] ?? $part;
                    $translated = cot_i18n_get_cat($part, $i18n_locale);
                    if ($translated && !empty($translated['title'])) {
                        $partTitle = $translated['title'];
                    }
                    $titleParts[] = $partTitle;
                }
                $tpath = implode($tpath_separator, $titleParts);
            }

            $result_array[$i] = $tpath;
        }
    }

    return cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), true);
}