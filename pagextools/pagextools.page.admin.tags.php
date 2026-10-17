<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.admin.tags
Order=10
[END_COT_EXT]
==================== */

/**
 * Page XTools - template tags
 *
 * Purpose: Adds the SEARCH_* tags used by the filter/search form in page.admin.tpl.
 * Must run after the base template has been assigned, before parsing.
 *
 * Plugin for Cotonti v.1+, PHP 8.5+
 * Filename: plugins/pagextools/pagextools.page.admin.tags.php
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

require_once cot_incfile('pagextools', 'plug');
// require_once      — подключает файл ровно один раз за запрос
// cot_incfile(...)  — Cotonti-хелпер: собирает путь к файлу расширения
//   'pagextools'    — код плагина (папка plugins/pagextools/)
//   'plug'          — тип расширения: плагин (а не 'module')
// (3-й параметр $part не указан → подставляется дефолт 'functions'
//  → фактически подключается: plugins/pagextools/inc/pagextools.functions.php
//  — файл с функциями cot_pagextools_*)

$pxt_sq = cot_import('sq', 'G', 'TXT');
// $pxt_sq           — локальная переменная для поисковой строки
// cot_import(...)   — Cotonti-хелпер: безопасно достаёт значение из запроса
//   'sq'            — имя параметра (?sq=...)
//   'G'             — источник: GET (можно 'P' — POST, 'R' — REQUEST, 'C' — COOKIE)
//   'TXT'           — фильтр: строка в кодировке сайта (обрезает хвосты, чистит)
// Результат: строка поискового запроса либо '' если параметра нет

$pxt_c  = cot_import('c', 'G', 'ALP');
// $pxt_c            — код выбранной категории
// cot_import(...)   — тот же хелпер
//   'c'             — имя параметра (?c=...)
//   'G'             — источник: GET
//   'ALP'           — фильтр: только буквы и цифры (alpha-numeric),
//                     остальное вырезается. Это защищает от инъекций
//                     и гарантирует, что код категории — валидный
//                     идентификатор. Пустая строка, если ?c= нет.


$t->assign([

    // ─────────────────────────────────────────────────────────────────────
    // URL формы поиска
    // ─────────────────────────────────────────────────────────────────────
    'SEARCH_ACTION_URL'  => cot_url('admin', 'm=page', '', true),
    // 'SEARCH_ACTION_URL'  — имя тега в шаблоне: {SEARCH_ACTION_URL}
    // cot_url(...)         — генерирует URL для action="" формы поиска
    //   'admin'            — area: страница админки
    //   'm=page'           — параметр m=page (модуль Pages)
    //   ''                 — tail (якорь), не нужен
    //   true               — вернуть с &amp; в HTML-виде (для action формы)

    // ─────────────────────────────────────────────────────────────────────
    // Текстовый input поиска
    // ─────────────────────────────────────────────────────────────────────
    'SEARCH_SQ'          => cot_inputbox('text', 'sq', $pxt_sq, 'class="schstring"'),
    // 'SEARCH_SQ'          — имя тега в шаблоне: {SEARCH_SQ}
    // cot_inputbox(...)    — рендерит <input>
    //   'text'             — type="text"
    //   'sq'               — name="sq" (уйдёт в URL как ?sq=...)
    //   $pxt_sq            — текущее значение из ?sq=
    //   'class="schstring"'— CSS-класс поля

	// ─────────────────────────────────────────────────────────────────────────
	// SEARCH_CAT_SELECT2 — старый тег, категория ПРИНУДИТЕЛЬНО устанавливается
	// ─────────────────────────────────────────────────────────────────────────
	'SEARCH_CAT_SELECT2'    => cot_pagextools_selectcat_select2(
		$pxt_c,     // 1. $check       — код категории из ?c=. Если он есть,
					//                   <option> с этим value получит " selected",
					//                   т.е. категория будет выбрана в списке
		'c'         // 2. $name        — имя HTML-поля (name="c"), уходит в URL как ?c=...
	),

	// ─────────────────────────────────────────────────────────────────────────
	// SEARCH_CAT_SELECT2_NO — новый тег, категория НЕ устанавливается
	// ─────────────────────────────────────────────────────────────────────────
	'SEARCH_CAT_SELECT2_CAT_EMPTY' => cot_pagextools_selectbox_structure_select2(
		'page',     // 1. $extension   — код расширения, чью структуру категорий рендерим
		'',         // 2. $check       — что пометить как " selected". Пусто = ни одна
					//                   категория не помечена, браузер подсветит первый
					//                   <option> (после фикса это "---")
		'c',        // 3. $name        — имя HTML-поля (name="c"), уходит в URL как ?c=...
		'',         // 4. $subcat      — показать только подкатегории указанной категории
					//                   ('' = показать все категории)
		true,       // 5. $hidePrivate — скрывать приватные категории (без права чтения)
		true,       // 6. $isModule    — проверять права как у модуля (а не плагина)
		true        // 7. $addEmpty    — добавить пустой пункт "---" первым в списке
	),

    // ─────────────────────────────────────────────────────────────────────
    // SEARCH_CAT — fallback-селект без Select2 (обычный <select>)
    // ─────────────────────────────────────────────────────────────────────
    'SEARCH_CAT'         => cot_pagextools_selectcat($pxt_c, 'c'),
    // 'SEARCH_CAT'         — имя тега в шаблоне: {SEARCH_CAT}
    // cot_pagextools_selectcat(...) — обычный <select> через cot_selectbox
    //   $pxt_c             — выбранный код категории из ?c=
    //   'c'                — name="c", уходит в URL как ?c=...
    // (внутри cot_selectbox(..., true) — последний true добавляет "---")

    // ─────────────────────────────────────────────────────────────────────
    // CATTITLE — заголовок выбранной категории для вывода рядом с формой
    // ─────────────────────────────────────────────────────────────────────
	'CATTITLE'           => (!empty($pxt_c) && isset(Cot::$structure['page'][$pxt_c]))
								? htmlspecialchars(Cot::$structure['page'][$pxt_c]['title'])
								: '',
	// 'CATTITLE'           — имя тега в шаблоне: {CATTITLE}
	// !empty($pxt_c)       — категория выбрана (параметр ?c= не пуст)
	// isset(...[$pxt_c])   — и такой код реально есть в структуре Page
	// htmlspecialchars(...) — название категории, безопасно для HTML
	// : ''                 — если не выбрана — пустая строка

]);
