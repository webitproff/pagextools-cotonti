<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.admin.first
Order=10
[END_COT_EXT]
==================== */

/**
 * Page XTools - admin search/filter bootstrap
 *
 * Purpose: 
 * Runs before Pages admin SQL queries are built. Replaces the default category
 * filter with the one taken from the "c" GET param, and appends LIKE-conditions
 * for the "sq" search string. Also rebuilds $urlParams and $backUrl so that
 * pagination and back-links keep the active filters.
 *
 * Plugin for Cotonti v.1+, PHP 8.5+
 * Filename: plugins/pagextools/pagextools.page.admin.first.php
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

require_once cot_incfile('pagextools', 'plug', 'functions');
require_once cot_langfile('pagextools', 'plug');

$pxt_sq = cot_import('sq', 'G', 'TXT');
$pxt_c  = cot_import('c', 'G', 'ALP');

/**
 * Default filter is "all" instead of the base "valqueue".
 * Without this, opening the page always hides published items.
 */
if (!isset($_REQUEST['filter']) || $_REQUEST['filter'] === '') {
    $filter   = 'all';
    $sqlwhere = '1 ';
}

/**
 * Replace the base "page_cat IN (...)" clause with our own,
 * derived from the user-selected category $pxt_c.
 */
if (!empty($pxt_c) && isset(Cot::$structure['page'][$pxt_c])) {
    $sqlwhere = preg_replace('/\s+AND\s+page_cat\s+IN\s*\([^)]+\)/i', '', $sqlwhere);

    $pxt_catsub = cot_structure_children('page', $pxt_c);
    if (count($pxt_catsub) < count(Cot::$structure['page'])) {
        $sqlwhere .= " AND page_cat IN ('" . implode("','", $pxt_catsub) . "')";
    }
}

/**
 * Multi-word text search across page_title and page_text.
 * Each word must be present (AND-like semantics).
 */
if (!empty($pxt_sq)) {
    $pxt_words = preg_split('/\s+/u', trim($pxt_sq));
    $pxt_words = array_filter($pxt_words, static fn($w) => $w !== '');

    if ($pxt_words !== []) {
        $pxt_pattern = '%' . implode('%', $pxt_words) . '%';
        $pxt_escaped = Cot::$db->prep($pxt_pattern);
        $sqlwhere .= " AND (page_title LIKE '" . $pxt_escaped . "' OR page_text LIKE '" . $pxt_escaped . "')";
    }
}

/**
 * Rebuild URL params. "all" is now the default filter, so it is not stored
 * in the URL. Custom search/filter values are kept for pagination.
 */
$urlParams = ['m' => 'page'];
if ($sorttype != 'id')   { $urlParams['sorttype'] = $sorttype; }
if ($sortway  != 'desc') { $urlParams['sortway']  = $sortway;  }
if ($filter   != 'all')  { $urlParams['filter']   = $filter;   }
if (!empty($pxt_sq))     { $urlParams['sq']       = $pxt_sq;   }
if (!empty($pxt_c))      { $urlParams['c']        = $pxt_c;    }

$common_params = http_build_query($urlParams, '', '&');

if ($pg > 1) {
    $urlParams['d'] = $durl;
}

$backUrl = cot_import('back', 'G', 'HTM');
$backUrl = !empty($backUrl) ? base64_decode($backUrl) : cot_url('admin', $urlParams, '', true);