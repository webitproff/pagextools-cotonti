<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.mainpanel
Order=10
[END_COT_EXT]
==================== */

/**
 * Page XTools — stats widget for the admin home mainpanel.
 *
 * Shows Pages-module statistics in the admin dashboard
 * (same hook as the Market PRO widget, order = 10).
 *
 * Filename: plugins/pagextools/pagextools.admin.home.php
 * Hooks:    admin.home.mainpanel
 * Date:     Oct 23, 2026
 *
 * @package pagextools
 * @version 1.0.0
 * @author webitproff
 * @copyright (c) 2026 webitproff | https://github.com/webitproff
 * @license BSD-3-Clause
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pagextools', 'plug');

/* ------------------------------------------------------------------
 * ACCESS CHECK
 * ------------------------------------------------------------------ */
list($auth_read, $auth_write, $isadmin) = cot_auth('page', 'any');
if (!$isadmin) {
    return;
}

/* ------------------------------------------------------------------
 * REQUIREMENTS
 * ------------------------------------------------------------------ */
require_once cot_incfile('page', 'module');

$db_pages = Cot::$db->pages;
$db_users = Cot::$db->users;

$now        = (int) Cot::$sys['now'];
$dayStart   = strtotime('today 00:00:00', $now);
$weekStart  = $now - 7 * 86400;
$monthStart = $now - 30 * 86400;

/* ------------------------------------------------------------------
 * STATS
 * ------------------------------------------------------------------ */
$stats = [
    'total'     => 0,
    'published' => 0,
    'pending'   => 0,
    'drafts'    => 0,
    'today'     => 0,
    'week'      => 0,
    'month'     => 0,
    'views'     => 0,
];

$rows = Cot::$db->query(
    "SELECT page_state, COUNT(*) AS cnt
     FROM $db_pages
     GROUP BY page_state"
)->fetchAll();

foreach ($rows as $r) {
    $cnt = (int) $r['cnt'];
    $stats['total'] += $cnt;
    switch ((int) $r['page_state']) {
        case PageDictionary::STATE_PUBLISHED: $stats['published'] = $cnt; break;
        case PageDictionary::STATE_PENDING:   $stats['pending']   = $cnt; break;
        case PageDictionary::STATE_DRAFT:     $stats['drafts']    = $cnt; break;
    }
}

$stats['today'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_pages WHERE page_date >= ?", [$dayStart]
)->fetchColumn();

$stats['week'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_pages WHERE page_date >= ?", [$weekStart]
)->fetchColumn();

$stats['month'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_pages WHERE page_date >= ?", [$monthStart]
)->fetchColumn();

$stats['views'] = (int) Cot::$db->query(
    "SELECT COALESCE(SUM(page_count), 0) FROM $db_pages"
)->fetchColumn();

$pct = function ($n) use ($stats) {
    return $stats['total'] > 0 ? round($n / $stats['total'] * 100, 1) : 0;
};

/* ------------------------------------------------------------------
 * TEMPLATE
 * ------------------------------------------------------------------ */
$tpl_Path = cot_tplfile('pagextools.admin.home', 'plug', true);
if (empty($tpl_Path)) {
    cot_error('Шаблон не найден');
}

$tt = new XTemplate($tpl_Path);
$tt->assign('TPL_PATH', Cot::$sys['abs_url'] . $tpl_Path);

/* ------------------------------------------------------------------
 * BASE TAGS
 * ------------------------------------------------------------------ */
$tt->assign([
    'ADMIN_HOME_URL'             => cot_url('admin', 'm=page'),
    'ADMIN_HOME_CONFIG_URL'      => cot_url('admin', 'm=config&n=edit&o=module&p=page'),
    'ADMIN_HOME_STRUCTURE_URL'   => cot_url('admin', 'm=structure&n=page'),
    'ADMIN_HOME_EXTRAFIELDS_URL' => cot_url('admin', 'm=extrafields&n=' . $db_pages),
    'ADMIN_HOME_ADD_URL'         => cot_url('page', 'm=add'),
    'ADMIN_HOME_QUEUE_URL'       => cot_url('admin', 'm=page&filter=valqueue'),

    'ADMIN_HOME_TOTAL'      => $stats['total'],
    'ADMIN_HOME_PUBLISHED'  => $stats['published'],
    'ADMIN_HOME_PENDING'    => $stats['pending'],
    'ADMIN_HOME_DRAFTS'     => $stats['drafts'],

    'ADMIN_HOME_TODAY'      => $stats['today'],
    'ADMIN_HOME_WEEK'       => $stats['week'],
    'ADMIN_HOME_MONTH'      => $stats['month'],
    'ADMIN_HOME_VIEWS'      => number_format($stats['views'], 0, '.', ' '),

    'ADMIN_HOME_PUBLISHED_PCT' => $pct($stats['published']),
    'ADMIN_HOME_PENDING_PCT'   => $pct($stats['pending']),
    'ADMIN_HOME_DRAFTS_PCT'    => $pct($stats['drafts']),

    'ADMIN_HOME_HAS_PENDING' => $stats['pending'] > 0,
    'ADMIN_HOME_IS_EMPTY'    => $stats['total'] === 0,
    'ADMIN_HOME_HAS_ITEMS'   => $stats['total'] > 0,
]);

/* ------------------------------------------------------------------
 * RECENT PAGES
 * ------------------------------------------------------------------ */
$recentItems = Cot::$db->query(
    "SELECT p.page_id, p.page_alias, p.page_title, p.page_cat,
            p.page_state, p.page_date, p.page_ownerid,
            u.user_name
     FROM $db_pages AS p
     LEFT JOIN $db_users AS u ON u.user_id = p.page_ownerid
     ORDER BY p.page_date DESC
     LIMIT 5"
)->fetchAll();

$stateMap = [
    PageDictionary::STATE_PUBLISHED => ['class' => 'success',   'lang' => 'page_status_published'],
    PageDictionary::STATE_PENDING   => ['class' => 'warning',   'lang' => 'page_status_pending'],
    PageDictionary::STATE_DRAFT     => ['class' => 'secondary', 'lang' => 'page_status_draft'],
];

if (!empty($recentItems)) {
    foreach ($recentItems as $item) {
        $state    = (int) $item['page_state'];
        $map      = $stateMap[$state] ?? ['class' => 'secondary', 'lang' => 'page_status_draft'];
        $catTitle = Cot::$structure['page'][$item['page_cat']]['title'] ?? $item['page_cat'];

        $tt->assign([
            'RECENT_ITEM_ID'          => $item['page_id'],
            'RECENT_ITEM_TITLE'       => htmlspecialchars($item['page_title']),
            'RECENT_ITEM_URL'         => cot_page_url($item),
            'RECENT_ITEM_CAT'         => htmlspecialchars($catTitle),
            'RECENT_ITEM_DATE'        => cot_date('datetime_short', $item['page_date']),
            'RECENT_ITEM_STATE_CLASS' => $map['class'],
            'RECENT_ITEM_STATE_LANG'  => Cot::$L[$map['lang']] ?? '',
            'RECENT_ITEM_OWNER'       => htmlspecialchars($item['user_name'] ?? ''),
        ]);
        $tt->parse('MAIN.RECENT_ITEMS.RECENT_ROW');
    }
    $tt->parse('MAIN.RECENT_ITEMS');
} else {
    $tt->parse('MAIN.RECENT_ITEMS.RECENT_EMPTY');
    $tt->parse('MAIN.RECENT_ITEMS');
}

/* ------------------------------------------------------------------
 * TOP-5 CATEGORIES
 * ------------------------------------------------------------------ */
$topCats = Cot::$db->query(
    "SELECT page_cat, COUNT(*) AS cnt
     FROM $db_pages
     GROUP BY page_cat
     ORDER BY cnt DESC
     LIMIT 5"
)->fetchAll();

if (!empty($topCats)) {
    foreach ($topCats as $cat) {
        $catTitle = Cot::$structure['page'][$cat['page_cat']]['title'] ?? $cat['page_cat'];
        $tt->assign([
            'TOP_CAT_CODE'  => $cat['page_cat'],
            'TOP_CAT_TITLE' => htmlspecialchars($catTitle),
            'TOP_CAT_COUNT' => $cat['cnt'],
            'TOP_CAT_URL'   => cot_url('admin', 'm=page&c=' . urlencode($cat['page_cat'])),
        ]);
        $tt->parse('MAIN.TOP_CATS.TOP_CAT_ROW');
    }
    $tt->parse('MAIN.TOP_CATS');
} else {
    $tt->parse('MAIN.TOP_CATS.TOP_CATS_EMPTY');
    $tt->parse('MAIN.TOP_CATS');
}

/* ------------------------------------------------------------------
 * TOP-5 AUTHORS
 * ------------------------------------------------------------------ */
$topAuthors = Cot::$db->query(
    "SELECT p.page_ownerid, COUNT(*) AS cnt, u.user_name
     FROM $db_pages AS p
     LEFT JOIN $db_users AS u ON u.user_id = p.page_ownerid
     WHERE p.page_ownerid > 0
     GROUP BY p.page_ownerid
     ORDER BY cnt DESC
     LIMIT 5"
)->fetchAll();

if (!empty($topAuthors)) {
    foreach ($topAuthors as $author) {
        $tt->assign([
            'TOP_AUTHOR_ID'    => $author['page_ownerid'],
            'TOP_AUTHOR_NAME'  => htmlspecialchars($author['user_name'] ?? ('#' . $author['page_ownerid'])),
            'TOP_AUTHOR_COUNT' => $author['cnt'],
            'TOP_AUTHOR_URL'   => cot_url('users', 'm=details&id=' . $author['page_ownerid']),
        ]);
        $tt->parse('MAIN.TOP_AUTHORS.TOP_AUTHOR_ROW');
    }
    $tt->parse('MAIN.TOP_AUTHORS');
} else {
    $tt->parse('MAIN.TOP_AUTHORS.TOP_AUTHORS_EMPTY');
    $tt->parse('MAIN.TOP_AUTHORS');
}

/* ------------------------------------------------------------------
 * TOP-10 VIEWED
 * ------------------------------------------------------------------ */
$topViewed = Cot::$db->query(
    "SELECT page_id, page_alias, page_title, page_cat, page_count
     FROM $db_pages
     WHERE page_state = " . PageDictionary::STATE_PUBLISHED . "
     ORDER BY page_count DESC
     LIMIT 10"
)->fetchAll();

if (!empty($topViewed)) {
    foreach ($topViewed as $item) {
        $catTitle = Cot::$structure['page'][$item['page_cat']]['title'] ?? $item['page_cat'];
        $tt->assign([
            'TOP_VIEWED_ID'    => $item['page_id'],
            'TOP_VIEWED_TITLE' => htmlspecialchars($item['page_title']),
            'TOP_VIEWED_URL'   => cot_page_url($item),
            'TOP_VIEWED_CAT'   => htmlspecialchars($catTitle),
            'TOP_VIEWED_COUNT' => number_format((int) $item['page_count'], 0, '.', ' '),
        ]);
        $tt->parse('MAIN.TOP_VIEWED.TOP_VIEWED_ROW');
    }
    $tt->parse('MAIN.TOP_VIEWED');
} else {
    $tt->parse('MAIN.TOP_VIEWED.TOP_VIEWED_EMPTY');
    $tt->parse('MAIN.TOP_VIEWED');
}

/* ------------------------------------------------------------------
 * HOOK for other plugins
 * ------------------------------------------------------------------ */
foreach (cot_getextplugins('pagextools.admin.home.tags') as $pl) {
    include $pl;
}

$tt->parse('MAIN');
$line = $tt->text('MAIN');