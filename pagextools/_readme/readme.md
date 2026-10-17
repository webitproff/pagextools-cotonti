# Page XTools for Cotonti

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/webitproff/pagextools-cotonti)
[![Cotonti Compatibility](https://img.shields.io/badge/Cotonti-1.0-orange.svg)](https://github.com/Cotonti/Cotonti)
[![PHP](https://img.shields.io/badge/PHP-8.5%2B-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-BSD--3--Clause-lightgrey.svg)](https://github.com/webitproff/pagextools-cotonti/blob/main/License.txt)

A plugin for Cotonti CMS that adds to the **Pages** module admin panel a convenient category filter (with Select2 support) and full-text search across the page title and body. Sorting by table fields, status, and direction. Works without core modifications — all logic is implemented through native Cotonti hooks.

---

## 📑 Table of Contents

- [1. General Information](#1-general-information)
  - [1.1. What is Page XTools](#11-what-is-page-xtools)
  - [1.2. Key Features](#12-key-features)
  - [1.3. System Requirements](#13-system-requirements)
  - [1.4. Compatibility](#14-compatibility)
  - [1.5. Links](#15-links)
- [2. How It Works](#2-how-it-works)
  - [2.1. Architecture](#21-architecture)
  - [2.2. Plugin Structure](#22-plugin-structure)
  - [2.3. Hooks and Their Role](#23-hooks-and-their-role)
  - [2.4. How the Category Filter Works](#24-how-the-category-filter-works)
  - [2.5. How Search Works](#25-how-search-works)
  - [2.6. URL Parameters](#26-url-parameters)
  - [2.7. Security](#27-security)
- [3. Step-by-Step Installation](#3-step-by-step-installation)
  - [3.1. Download the Archive](#31-download-the-archive)
  - [3.2. Manual Installation via FTP/SFTP](#32-manual-installation-via-ftpsftp)
  - [3.3. Adding Code to the Template](#33-adding-code-to-the-template)
  - [3.4. Verification](#34-verification)
- [4. Step-by-Step Usage Guide](#4-step-by-step-usage-guide)
  - [4.1. Text Search](#41-text-search)
  - [4.2. Category Filter](#42-category-filter)
  - [4.3. Standard Cotonti Filter](#43-standard-cotonti-filter)
  - [4.4. Sorting](#44-sorting)
  - [4.5. Resetting Filters](#45-resetting-filters)
- [5. Step-by-Step Security Guide](#5-step-by-step-security-guide)
- [6. Possible Problems and Solutions](#6-possible-problems-and-solutions)
- [7. Logs and Diagnostics](#7-logs-and-diagnostics)
- [8. FAQ — Frequently Asked Questions](#8-faq--frequently-asked-questions)
- [9. Plugin Update](#9-plugin-update)
- [10. Plugin Removal](#10-plugin-removal)
- [11. Roadmap](#11-roadmap)
- [12. Changelog](#12-changelog)
- [13. Contributing](#13-contributing)
- [14. License](#14-license)
- [15. Contacts and Support](#15-contacts-and-support)

---

## 1. General Information

### 1.1. What is Page XTools

**Page XTools** is a plugin for [Cotonti CMF](https://github.com/Cotonti/Cotonti) that extends the standard **Pages** module admin panel:

- **quick text search** — across the page title and body;
- **category filter** — with Select2 and subcategory support;
- **friendly default** — the "All" filter instead of "In queue".

The plugin is installed as a regular Cotonti extension and **does not require core modifications** either during installation or during CMS updates. All logic runs through two hooks: `page.admin.first` and `page.admin.tags`.

### 1.2. Key Features

- **Full-text search** across `page_title` and `page_text` with multi-word support (AND semantics).
- **Category filter** — hierarchical (subcategories included).
- **Select2-compatible dropdown** — supports search and tree display.
- **Fallback** — if Select2 is not connected, a plain `<select>` is used.
- **Filters preserved in URL** — you can share a link with an active filter, and pagination works.
- **Multilingual** — localization files for `ru` and `en`.
- **No core modifications** — native Cotonti hooks only.

### 1.3. System Requirements

| Component | Minimum Version | Recommended |
|-----------|-----------------|-------------|
| PHP       | 8.4             | 8.5+        |
| MySQL     | 8.0             | 8.4+        |
| MariaDB   | 10.2            | 10.6+       |
| Cotonti   | 1.0.x           | Verona 1.0.x |
| Page module | must be active | — |

**PHP extensions:**
- `pdo_mysql` — required;
- `mbstring` — for Cyrillic and UTF-8 handling;
- `json` — built into PHP ≥ 8.0.

**Admin theme:**
- CotCP (recommended) — already includes Select2;
- any other theme — the plugin falls back to a plain `<select>`.

### 1.4. Compatibility

The plugin has been tested on:

- **Cotonti** 1.0.x;
- **MySQL** 8.4;
- **MariaDB** 10.2 — 10.11;
- **PHP** 8.5;
- **Apache** 2.4, **Nginx** (with php-fpm), **LiteSpeed**.

### 1.5. Links

- **Cotonti Marketplace:** [abuyfile.com/ru/market/cotonti/plugs/pagextools](https://abuyfile.com/ru/market/cotonti/plugs/pagextools)
- **GitHub Repository:** [github.com/webitproff/pagextools-cotonti](https://github.com/webitproff/pagextools-cotonti)
- **Forum Support:** [abuyfile.com/ru/forums/cotonti/custom/plugs](https://abuyfile.com/ru/forums/cotonti/custom/plugs)
- **Current admin theme for Cotonti [CotCP](https://github.com/webitproff/cotcp)**

---

## 2. How It Works

### 2.1. Architecture

The plugin consists of two handler files and one helper file with functions. No background processes, cron jobs, or database schema changes.

Workflow:

1. User opens `/admin.php?m=page`.
2. Cotonti core loads `page.admin.php`.
3. At the moment the core builds SQL queries for the pages list, the **`page.admin.first`** hook fires — the plugin replaces the category filter and appends LIKE search conditions.
4. The core executes the SQL and renders the template.
5. Before the template is parsed, the **`page.admin.tags`** hook fires — the plugin adds tags to the template:  
   `{SEARCH_SQ}` — keyword search field  
   `{SEARCH_CAT_SELECT2}` — category auto-selected  
   `{SEARCH_CAT_SELECT2_CAT_EMPTY}` — search without specifying a category  
   `{SEARCH_CAT}` — backward compatibility  
   `{CATTITLE}` — category title, if entered  
   `{SEARCH_ACTION_URL}`.  
6. The `page.admin.tpl` template renders the search and filter form.

### 2.2. Plugin Structure

```
plugins/pagextools/
├── pagextools.setup.php               # plugin metadata
├── pagextools.page.admin.first.php    # hook: SQL construction
├── pagextools.page.admin.tags.php     # hook: template tags
├── inc/
│   └── pagextools.functions.php       # rendering functions
└── lang/
    ├── pagextools.ru.lang.php         # Russian localization
    └── pagextools.en.lang.php         # English localization
```

### 2.3. Hooks and Their Role

| Hook | When Called | What the Plugin Does |
|------|-------------|----------------------|
| `page.admin.first` | after building base `$sqlwhere`, before SQL queries | replaces the category filter, appends LIKE search conditions, rebuilds `$urlParams` and `$backUrl` |
| `page.admin.tags`  | after standard `$t->assign([...])`, before parsing | adds tags `SEARCH_SQ`, `SEARCH_CAT_SELECT2`, `SEARCH_CAT`, `CATTITLE`, `SEARCH_ACTION_URL` |

**Important:** hooks are declared **in the handler files themselves** via the `[BEGIN_COT_EXT]` block, not in the setup file. Setup contains only metadata (`Code`, `Name`, `Version`, etc.).

### 2.4. How the Category Filter Works

1. User selects a category from the dropdown → `?c=cat_code`.
2. In the `page.admin.first` hook, the value `$pxt_c = cot_import('c', 'G', 'ALP')` is extracted and validated.
3. The standard `AND page_cat IN (...)` construct is removed from the base `$sqlwhere` via `preg_replace`.
4. In its place, a filter by the category and its subcategories is added via `cot_structure_children('page', $pxt_c)`.
5. If no category is selected, the standard Cotonti behavior is used.

### 2.5. How Search Works

1. User enters a string in the "Search" field → `?sq=word`.
2. The string is split by whitespace (`preg_split('/\s+/u', ...)`).
3. Each word is escaped via `Cot::$db->prep()`.
4. A LIKE pattern is assembled: `%word1%word2%`.
5. The condition is appended to `$sqlwhere`:
   ```sql
   AND (page_title LIKE '%word1%word2%' OR page_text LIKE '%word1%word2%')
   ```
6. Search works with multiple words and Cyrillic (UTF-8).

### 2.6. URL Parameters

| Parameter | Value | Description |
|-----------|-------|-------------|
| `m`      | `page`   | module (standard) |
| `sq`     | string   | search query |
| `c`      | code     | category code |
| `filter` | code     | standard Cotonti filter (all/valqueue/validated/drafts/expired) |
| `sorttype` | code   | sorting field |
| `sortway`  | code   | direction (asc/desc) |
| `d`      | number   | pagination page number |

All filters are stored in the URL, so they can be shared and work across page navigation.

### 2.7. Security

- **`cot_import('c', 'G', 'ALP')`** — the category is accepted only as an alphanumeric string (ALP), which rules out SQL injection and XSS.
- **`cot_import('sq', 'G', 'TXT')`** — the search string is cleaned by the standard Cotonti importer.
- **`Cot::$db->prep()`** — escapes special characters before substitution into LIKE.
- **`htmlspecialchars()`** — applied to all user data before insertion into HTML.
- **Access rights** — the plugin works only for Cotonti administrators with access to the Page module. Permission checks are performed by the core.
- **CSRF** — standard actions (validate, delete, update_checked) are protected by the native `cot_check_xg()` / `cot_check_xp()`, which the plugin does not touch.

---

## 3. Step-by-Step Installation

### 3.1. Download the Archive

**Download** the archive from [GitHub](https://github.com/webitproff/pagextools-cotonti).

### 3.2. Manual Installation via FTP/SFTP

1. Back up the site and database.
2. Unpack the downloaded archive locally on your PC.
3. Upload the `pagextools/` folder into `/plugins/` of your Cotonti site.
4. Ensure the structure is as follows:
   ```
   /plugins/pagextools/pagextools.setup.php
   /plugins/pagextools/pagextools.page.admin.first.php
   /plugins/pagextools/pagextools.page.admin.tags.php
   /plugins/pagextools/inc/pagextools.functions.php
   /plugins/pagextools/lang/pagextools.ru.lang.php
   /plugins/pagextools/lang/pagextools.en.lang.php
   ```
5. Log in to the Cotonti admin panel.
6. Go to **Extensions → Manage Plugins**.
7. Find **Page XTools** `pagextools` in the list and click **"Install"**.

### 3.3. Adding Code to the Template

**Important:** the plugin adds tags to the template but does not overwrite it.  
You need to insert the HTML block into your admin theme's `page.admin.tpl` yourself.

FIND THE DEFAULT TEMPLATE `modules/page/tpl/page.admin.tpl` AND MOVE IT INTO THE ADMIN THEME FOLDER, FOR EXAMPLE:
`themes/admin/cotcp/modules/page/page.admin.tpl`

IN THE DEFAULT TEMPLATE FIND THE BLOCK:
```
<!-- ---------------------------------------------------------------------- -->
	<!-- IF {ADMIN_PAGE_TOTALDBPAGES} -->
	<form name="form_valqueue" method="get" action="{PHP|cot_url('admin', 'm=page')}">
		<!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
		<input type="hidden" name="m" value="page" />
		<!-- ENDIF -->
		<table class="cells">
			<thead>
				<tr>
					<th class="coltop w-5"></th>
					<th class="coltop w-5"></th>
					<th class="coltop w-15 filterSelect">{ADMIN_PAGE_FILTER}</th>
					<th class="coltop w-60">
						<!-- IF {TOTAL_ENTRIES} > 1 -->
						{PHP.L.adm_sort} {ADMIN_PAGE_ORDER} {ADMIN_PAGE_WAY}
						<!-- ENDIF -->
					</th>
					<td class="coltop"><button type="submit">{PHP.L.Filter}</button></td>
				</tr>
			</thead>
		</table>
	</form>
	<!-- ENDIF -->
<!-- ---------------------------------------------------------------------- -->	
```
AND REPLACE IT ENTIRELY WITH THE BLOCK BELOW:
```
<!-- ---------------------------------------------------------------------- -->

<!-- IF {CATTITLE} -->
<div class="alert alert-info my-2" role="alert">
  {PHP.L.Category}: {CATTITLE}
</div>
<!-- ENDIF -->
<div class="card filter-section p-3 mb-4" style="border: 5px var(--bs-dark-border-subtle) solid">
  <!-- IF {ADMIN_PAGE_TOTALDBPAGES} -->
  <form name="form_valqueue" method="get" action="{SEARCH_ACTION_URL}" class="mb-3">
    <!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
    <input type="hidden" name="m" value="page" />
    <!-- ENDIF -->
    <div class="row g-2 align-items-end">
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.Search}</label>
        <div class="flex-grow-1">{SEARCH_SQ}</div>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.Category}</label>
        <div class="flex-grow-1 filterSelect">{SEARCH_CAT_SELECT2_CAT_EMPTY}</div>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.pagextools_filter_option_status}</label>
        <div class="flex-grow-1">{ADMIN_PAGE_FILTER}</div>
      </div>
      <!-- IF {TOTAL_ENTRIES} > 1 -->
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.pagextools_filter_sorting_fields}</label>
        <div class="flex-grow-1">{ADMIN_PAGE_ORDER}</div>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.pagextools_filter_sorting_direction}</label>
        <div class="flex-grow-1">{ADMIN_PAGE_WAY}</div>
      </div>
      <!-- ENDIF -->
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <button type="submit" class="btn btn-outline-primary w-100 mt-auto">
          <i class="fa-solid fa-filter me-1"></i>{PHP.L.pagextools_filter} </button>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
		<a class="btn btn-outline-danger w-100 mt-auto" href="{PHP|cot_url('admin','m=page')}"><i class="fa-solid fa-broom me-1"></i>{PHP.L.pagextools_prune} </a>
      </div>
    </div>
  </form>
  <!-- ENDIF -->
</div>

<!-- ---------------------------------------------------------------------- -->
```
At the end of the file add:
```
<style scoped>
    .filterSelect select {
        width: 100%;
    }
</style>
```
before the closing template block:
```
<!-- END: MAIN -->
```

### 3.4. Verification

1. Open **Tools → Pages** (`/admin.php?m=page`).
2. Check that a block with fields has appeared at the top:
   - **Search**;
   - **Category** (Select2 or a plain select);
   - **Filter** (standard Cotonti);
   - **Sorting** (if records > 1).
3. Enter a word in the "Search" field, e.g. `example` — click **"Filter"**.
4. Verify that the list has been filtered by `page_title` and `page_text`.
5. Select a category — verify that the list is filtered by it and its subcategories.
6. Click **"Reset"** — filters are cleared.

---

## 4. Step-by-Step Usage Guide

### 4.1. Text Search

1. In the **"Search"** field, enter a word or phrase (multiple words allowed).
2. Click **"Filter"**.
3. The list is narrowed to records whose `page_title` **or** `page_text` contains all entered words.

**Examples:**
- `hello` — finds all pages containing "hello".
- `hello world` — finds pages containing **both** "hello" **and** "world".
- Is search case-sensitive? **No**, the MySQL collation `utf8mb4_unicode_ci` is case-insensitive.

### 4.2. Category Filter

1. Open the **"Category"** dropdown.
2. Select the desired category (the tree displays subcategories as well).
3. Click **"Filter"**.
4. The list shows records from the selected category and all its subcategories.

**Resetting category:** select the empty option (`---`) or click **"Reset"**.

### 4.3. Standard Cotonti Filter

The **"Filter"** field is the standard Cotonti filter:

| Value | What it shows |
|-------|---------------|
| `All` | all pages (this is the plugin default) |
| `In queue` | `page_state = 1` (pending) |
| `Validated` | `page_state = 0` (published) |
| `Expired` | `page_begin > now OR (page_expire <> 0 AND page_expire < now)` |
| `Drafts` | `page_state = 2` (draft) |

**Note:** base Cotonti uses `valqueue` by default. The plugin changes the default to `all` so that all records are visible at once.

### 4.4. Sorting

- **"Sorting"** — sorting field (ID, title, date, publication date, author, etc.).
- **"Direction"** — ascending / descending.

Both parameters are preserved in the URL and work across page navigation.

### 4.5. Resetting Filters

The **"Reset"** button (broom icon) navigates to a clean `/admin.php?m=page` without any filters.

---

## 5. Step-by-Step Security Guide

The plugin does not introduce new risks, but for full security the following is recommended:

1. **Do not give access to the Pages admin to non-administrators.** The plugin works only for those who have access to the Page module. The check is performed by the Cotonti core.
2. **Use HTTPS.** Search queries and selected categories are passed in the URL, and without HTTPS they are visible in plain text.
3. **Limit the length of search queries** through PHP settings (e.g. `max_input_vars`, `post_max_size`). The plugin does not limit the length of `sq`, so a gigantic string could end up in the MySQL log.
4. **Do not remove the `cot_check_xg()` call from the code.** It is an XK-token check function (analogous to CSRF), not a setting — it cannot be enabled or disabled from the admin panel. It is either called in the handler file (e.g. in `page.admin.php` before the `validate` / `unvalidate` / `delete` / `update_checked` actions) or not. If someone removes this line from the code, the action becomes vulnerable to CSRF. The `pagextools` plugin itself does not touch `cot_check_xg()`.
5. **Update Cotonti regularly.** The plugin does not modify the core, so updating the CMS is always safe (provided the core structure does not change radically).
6. **Do not disable `.htaccess` and `index.php`** in the admin folder. This is basic Cotonti hygiene, but worth mentioning.
7. **Check the template before and after updating the admin theme.** If the theme is updated, your inserted block in `page.admin.tpl` may disappear. This is not a security risk, but it will require re-editing the template. **Backups always save you if you have them.**

---

## 6. Possible Problems and Solutions

### 6.1. Search Form Did Not Appear

**Symptom:** I open `/admin.php?m=page` — no form.

**Causes:**
- **Plugin is not installed** — check under **Extensions → Manage Plugins**.
- **Plugin is not activated** — activate it.
- **Code not added to `page.admin.tpl`** — check step 3.3.
- **Theme/template cache** — clear the Cotonti cache in the admin panel.

### 6.2. Error "Call to undefined function cot_pagextools_selectcat_select2()"

**Cause:** the functions file is not loaded.

**Solution:** make sure the file `plugins/pagextools/inc/pagextools.functions.php` exists and the hooks include the line:

```php
require_once cot_incfile('pagextools', 'plug');
```

### 6.3. SQL Error: "Unknown column ..."

**Cause:** a non-standard `page_*` field is used, or the table template has been modified.

**Solution:**
- Check the structure of the `cot_pages` table (`SHOW COLUMNS FROM cot_pages`).
- The search in the plugin works on `page_title` and `page_text`. If they are absent, the plugin is invalid.

### 6.4. Select2 Does Not Work

**Symptom:** the category select looks like a plain `<select>`.

**Causes:**
- Admin theme without Select2 — the plugin falls back to `cot_pagextools_selectcat()` (plain select). This is **normal behavior**, not a bug.
- Select2 is connected, but the JS init did not fire — check the browser console.

**Solution:**
- Install the **[CotCP](https://github.com/webitproff/cotcp)** theme — it already includes Select2 compatibility for the Pages module admin template.
- Or add the Select2 init to your own admin template.

### 6.5. Search Finds Nothing

**Cause:** it searches `page_title` and `page_text`. If the sought phrase is in `page_desc` or meta-fields, the search won't find it.

**Solution:**
- Extend the search by adding `page_desc` and/or `page_keywords` fields to `$sqlwhere`. This is done in `pagextools.page.admin.first.php`:

```php
$sqlwhere .= " AND (page_title LIKE '...' OR page_text LIKE '...' OR page_desc LIKE '...')";
```

- Or use the standard Cotonti search (`/index.php?e=search`).

### 6.6. Search Does Not Find Cyrillic

**Cause:** the database is in `latin1` or the connection is not in `utf8mb4`.

**Solution:**
- Check the database encoding: `SHOW CREATE DATABASE your_db;` — it must be `utf8mb4`.
- Check `datas/config.php` → `$cfg['mysqlcharset']` — it must be `utf8mb4`.

### 6.7. Category Filter Does Not Work

**Cause:** the category code contains special characters or the category is not in the structure.

**Solution:**
- Verify that the category exists: `Cot::$structure['page'][$code]`.
- Verify that the category code is alphanumeric (no spaces, no hyphens).

### 6.8. Form Disappeared After Admin Theme Update

**Cause:** the theme update overwrote `page.admin.tpl`.

**Solution:**
- Re-insert the code from step 3.3.

---

## 7. Logs and Diagnostics

### 7.1. Where to Look

| What | Where |
|------|-------|
| PHP errors | `/var/log/php/error.log`, site `error_log` |
| MySQL errors | `/var/log/mysql/error.log` |
| Apache errors | `/var/log/apache2/error.log` |
| Nginx errors | `/var/log/nginx/error.log` |
| Cotonti debug | `datas/config.php` → `$cfg['debug_mode'] = true` |

### 7.2. Enabling Verbose Logging

In `datas/config.php`:

```php
$cfg['debug_mode'] = true;
$cfg['devmode']    = true;
```

```
@SEE
system/footer.php
if (Cot::$cfg['devmode'] && cot_auth('admin', 'a', 'A')) {
```

After that, all SQL queries (including plugin queries) will be visible in the Cotonti debug area with execution time and stack trace.

**Attention:** do not leave these flags enabled in production.

---

## 8. FAQ — Frequently Asked Questions

**Q: Does the plugin work without Select2?**
A: Yes. If Select2 is not connected, a plain `<select>` is used via `cot_pagextools_selectcat()`.

**Q: Is search case-sensitive?**
A: No. The `utf8mb4_unicode_ci` collation in MySQL/MariaDB is case-insensitive.

**Q: Does search work on description (`page_desc`)?**
A: No, by default only on `page_title` and `page_text`. Can be extended by editing the `page.admin.first` hook.

**Q: Can I search on meta-fields (`page_keywords`, `page_metatitle`)?**
A: Not natively. Requires editing the hook.

**Q: Does it work on Cotonti Verona 1.0.x?**
A: Yes, starting from plugin version 1.0.0. Uses `Cot::$db`, `Cot::$structure` — compatible.

**Q: Is the plugin compatible with Cotonti Siena 0.9.x?**
A: Yes, starting from 1.0.0.

**Q: Do I need to modify the core?**
A: No. Everything is via hooks. When Cotonti is updated, the plugin continues to work.

**Q: What happens if I disable the plugin?**
A: The search and filter form disappears, and the rest of the admin behavior returns to standard. The plugin does not store any data in the database.

**Q: Can I add sorting by date?**
A: Yes, that's a standard Cotonti feature — the `sorttype` field in the form already contains all fields, including `date`.

**Q: Does search slow down on a large database?**
A: Yes, if there are no indexes on `page_title` and `page_text`. For large tables (100k+ records) add a FULLTEXT index:

```sql
ALTER TABLE cot_pages ADD FULLTEXT(page_title, page_text);
```

and use `MATCH ... AGAINST` instead of `LIKE`. This requires editing the hook.

**Q: Does the plugin support multilingual?**
A: Yes, there are `pagextools.ru.lang.php` and `pagextools.en.lang.php`.

**Q: Can I use the plugin on multiple sites?**
A: Yes. It is not tied to a domain.

---

## 9. Plugin Update

1. **Make a backup** of the plugin files and (just in case) the database.
2. Download the new version from the marketplace or GitHub.
3. In the Cotonti admin panel → **Extensions → Manage Plugins → Page XTools → Update**.
4. If updating via the admin panel is unavailable — unpack over the old `plugins/pagextools/` folder.
5. Verify that the files `pagextools.page.admin.first.php` and `pagextools.page.admin.tags.php` have not lost the `[BEGIN_COT_EXT]` block with hooks.
6. Open `/admin.php?m=page` and verify functionality.

---

## 10. Plugin Removal

1. In the Cotonti admin panel → **Extensions → Manage Plugins → Page XTools → Delete**.
2. Delete the `plugins/pagextools/` folder manually if it remains.
3. **Remove the form block** from `themes/admin/{theme}/modules/page/page.admin.tpl` (otherwise the dangling `{SEARCH_SQ}` tags will remain — Cotonti will simply ignore them, but cleanliness matters).
4. Remove the `<style scoped>...</style>` at the end of the template.
5. Clear the Cotonti cache.

**Important:** the plugin does not store data in the database, so removal is safe.

---

## 11. Roadmap

- [ ] **Extended search** — include `page_desc`, `page_keywords`, `page_metatitle`.
- [ ] **FULLTEXT index** — optional setting via config.
- [ ] **Multi-filter by multiple categories** — multiple selection.
- [ ] **Save filters to cookie** — so that filters are restored when the admin panel is opened.
- [ ] **Date range** — filter by `page_date` / `page_begin` / `page_expire`.
- [ ] **Author** — filter by `page_ownerid`.
- [ ] **List export** — export the filtered list to CSV.
- [ ] **Postgres support** — if Cotonti moves to other DBMS.
- [ ] **Advanced sorting** — multiple fields.
- [ ] **Search on extrafields** — on configurable fields.

---

## 12. Changelog

### v1.0.0 (2026-10-17)

- First public release.
- Hooks `page.admin.first` and `page.admin.tags`.
- Search on `page_title` and `page_text` with multi-word support.
- Category filter with subcategories.
- Select2-compatible select + fallback.
- Default filter changed from `valqueue` to `all`.
- Localization `ru` / `en`.
- Metadata in the setup file, hooks in separate files.

---

## 13. Contributing

Pull requests are welcome. Before submitting:

1. Fork the [repository](https://github.com/webitproff/pagextools-cotonti).
2. Create a branch `feature/your-feature` or `fix/your-bug`.
3. Follow PSR-12.
4. Add tests if possible.
5. Update `README.md` and `CHANGELOG`.
6. Submit a PR (Pull-request) with a description: what, why, how to test.

**Code rules:**
- Function names — prefixed with `cot_pagextools_`.
- No core modifications.
- All SQL parameters — via `Cot::$db->prep()` or binding.
- All user data in HTML — via `htmlspecialchars()`.
- Multilingual — via `$L['pagextools_*']`.

---

## 14. License

BSD 3-Clause. Full text is in the `License.txt` file.

---

## 15. Contacts and Support

- **Cotonti Marketplace:** [abuyfile.com/ru/market/cotonti/plugs/pagextools](https://abuyfile.com/ru/market/cotonti/plugs/pagextools)
- **GitHub Repository:** [github.com/webitproff/pagextools-cotonti](https://github.com/webitproff/pagextools-cotonti)
- **Support Forum:** [abuyfile.com/ru/forums/cotonti/custom/plugs](https://abuyfile.com/ru/forums/cotonti/custom/plugs)
- **Author:** [webitproff](https://abuyfile.com/ru/users/webitproff)
- **Issues:** [github.com/webitproff/pagextools-cotonti/issues](https://github.com/webitproff/pagextools-cotonti/issues)

___


# (РУССКИЙ) Page XTools для Cotonti

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/webitproff/pagextools-cotonti)
[![Cotonti](https://img.shields.io/badge/Cotonti%201.0-orange.svg)](https://github.com/Cotonti/Cotonti)
[![PHP](https://img.shields.io/badge/PHP-8.5%2B-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-BSD--3--Clause-lightgrey.svg)](https://github.com/webitproff/pagextools-cotonti/blob/main/License.txt)

Плагин для CMS Cotonti, добавляющий в админ-панель модуля **Pages** удобный фильтр по категориям (с поддержкой Select2) и полнотекстовый поиск по заголовку и телу страницы. Сортировка по полям таблицы, статусу и направлению. Работает без правок ядра — вся логика реализована через штатные хуки Cotonti. 

---

## 📑 Оглавление

- [1. Общие сведения](#1-общие-сведения)
  - [1.1. Что такое Page XTools](#11-что-такое-page-xtools)
  - [1.2. Ключевые возможности](#12-ключевые-возможности)
  - [1.3. Системные требования](#13-системные-требования)
  - [1.4. Совместимость](#14-совместимость)
  - [1.5. Ссылки](#15-ссылки)
- [2. Принципы работы](#2-принципы-работы)
  - [2.1. Архитектура](#21-архитектура)
  - [2.2. Структура плагина](#22-структура-плагина)
  - [2.3. Хуки и их роль](#23-хуки-и-их-роль)
  - [2.4. Как работает фильтр по категориям](#24-как-работает-фильтр-по-категориям)
  - [2.5. Как работает поиск](#25-как-работает-поиск)
  - [2.6. Параметры URL](#26-параметры-url)
  - [2.7. Безопасность](#27-безопасность)
- [3. Пошаговая установка](#3-пошаговая-установка)
  - [3.1. Через админку Cotonti](#31-через-админку-cotonti)
  - [3.2. Вручную через FTP/SFTP](#32-вручную-через-ftpsftp)
  - [3.3. Добавление кода в шаблон](#33-добавление-кода-в-шаблон)
  - [3.4. Проверка работоспособности](#34-проверка-работоспособности)
- [4. Пошаговое руководство по использованию](#4-пошаговое-руководство-по-использованию)
  - [4.1. Поиск по тексту](#41-поиск-по-тексту)
  - [4.2. Фильтр по категории](#42-фильтр-по-категории)
  - [4.3. Стандартный фильтр Cotonti](#43-стандартный-фильтр-cotonti)
  - [4.4. Сортировка](#44-сортировка)
  - [4.5. Сброс фильтров](#45-сброс-фильтров)
- [5. Пошаговое руководство по безопасности](#5-пошаговое-руководство-по-безопасности)
- [6. Возможные проблемы и их решения](#6-возможные-проблемы-и-их-решения)
- [7. Логи и диагностика](#7-логи-и-диагностика)
- [8. FAQ — часто задаваемые вопросы](#8-faq--часто-задаваемые-вопросы)
- [9. Обновление плагина](#9-обновление-плагина)
- [10. Удаление плагина](#10-удаление-плагина)
- [11. Дорожная карта](#11-дорожная-карта)
- [12. История изменений](#12-история-изменений)
- [13. Участие в разработке](#13-участие-в-разработке)
- [14. Лицензия](#14-лицензия)
- [15. Контакты и поддержка](#15-контакты-и-поддержка)

---

## 1. Общие сведения

### 1.1. Что такое Page XTools

**Page XTools** — это плагин для [Cotonti CMF](https://github.com/Cotonti/Cotonti), который расширяет стандартную админ-панель модуля **Pages**:

- **быстрый поиск по тексту** — по заголовку и содержимому страницы;
- **фильтр по категории** — с поддержкой Select2 и подкатегорий;
- **дружелюбный дефолт** — фильтр «Все» вместо «В очереди».

Плагин устанавливается как обычное расширение Cotonti и **не требует правок ядра** ни при установке, ни при обновлении CMS. Вся логика работает через два хука: `page.admin.first` и `page.admin.tags`.

### 1.2. Ключевые возможности

- **Полнотекстовый поиск** по `page_title` и `page_text` с поддержкой нескольких слов (AND-семантика).
- **Фильтр по категории** — с учётом иерархии (включая подкатегории).
- **Select2-совместимый выпадающий список** — поддерживает поиск и отображение дерева.
- **Fallback-функционал** — если Select2 не подключён, используется обычный ``.
- **Сохранение фильтров в URL** — можно делиться ссылкой с активным фильтром, работает навигация по страницам.
- **Мультиязычность** — файлы локализации для `ru` и `en`.
- **Никаких правок ядра** — только штатные хуки Cotonti.

### 1.3. Системные требования

| Компонент | Минимальная версия | Рекомендуемая |
|-----------|--------------------|---------------|
| PHP       | 8.4                | 8.5+          |
| MySQL     | 8.0                | 8.4+          |
| MariaDB   | 10.2               | 10.6+         |
| Cotonti   | 1.0.x              | Verona 1.0.x  |
| Модуль Page | должен быть активен | — |

**Расширения PHP:**
- `pdo_mysql` — обязательно;
- `mbstring` — для работы с кириллицей и UTF-8;
- `json` — встроен в PHP ≥ 8.0.

**Тема админки:**
- CotCP (рекомендуется) — уже включает Select2;
- любая другая тема — плагин откатится на обычный ``.

### 1.4. Совместимость

Плагин протестирован на:

- **Cotonti** 1.0.x;
- **MySQL** 8.4;
- **MariaDB** 10.2 — 10.11;
- **PHP** 8.5;
- **Apache** 2.4, **Nginx** (с php-fpm), **LiteSpeed**.

### 1.5. Ссылки

- **Маркетплейс Cotonti:** [abuyfile.com/ru/market/cotonti/plugs/pagextools](https://abuyfile.com/ru/market/cotonti/plugs/pagextools)
- **Репозиторий на GitHub:** [github.com/webitproff/pagextools-cotonti](https://github.com/webitproff/pagextools-cotonti)
- **Поддержка на форуме:** [abuyfile.com/ru/forums/cotonti/custom/plugs](https://abuyfile.com/ru/forums/cotonti/custom/plugs)  
- **Актуальная тема админки для Cotonti [CotCP](https://github.com/webitproff/cotcp)**

---

## 2. Принципы работы

### 2.1. Архитектура

Плагин состоит из двух файлов-обработчиков и одного вспомогательного файла с функциями. Никаких фоновых процессов, крон-задач или изменений в схеме БД нет.

Поток работы:

1. Пользователь открывает `/admin.php?m=page`.
2. Ядро Cotonti запускает `page.admin.php`.
3. В момент, когда ядро собирает SQL-запросы списка страниц, срабатывает хук **`page.admin.first`** — плагин подменяет фильтр по категории и добавляет LIKE-условия поиска.
4. Далее ядро выполняет SQL и рендерит шаблон.
5. Перед парсингом шаблона срабатывает хук **`page.admin.tags`** — плагин добавляет в шаблон теги 
`{SEARCH_SQ}`, - форма поиска по ключевому слову  
`{SEARCH_CAT_SELECT2}`, - категория подставляется автоматически   
`{SEARCH_CAT_SELECT2_CAT_EMPTY}`, - позволяет выполнять поиск, не указывая категорию   
`{SEARCH_CAT}`, - обратная совместимость  
`{CATTITLE}`, заголовок категории, если зашли в нее  
`{SEARCH_ACTION_URL}`.  
6. Шаблон `page.admin.tpl` отображает форму поиска и фильтра.

### 2.2. Структура плагина

```
plugins/pagextools/
├── pagextools.setup.php               # метаданные плагина
├── pagextools.page.admin.first.php    # хук: построение SQL
├── pagextools.page.admin.tags.php     # хук: теги для шаблона
├── inc/
│   └── pagextools.functions.php       # функции рендеринга 
└── lang/
    ├── pagextools.ru.lang.php         # русская локализация
    └── pagextools.en.lang.php         # английская локализация
```

### 2.3. Хуки и их роль

| Хук | Когда вызывается | Что делает плагин |
|-----|------------------|-------------------|
| `page.admin.first` | после построения базового `$sqlwhere`, до SQL-запросов | подменяет фильтр по категории, добавляет LIKE-условия поиска, пересобирает `$urlParams` и `$backUrl` |
| `page.admin.tags`  | после стандартного `$t-&gt;assign([...])`, до парсинга | добавляет теги `SEARCH_SQ`, `SEARCH_CAT_SELECT2`, `SEARCH_CAT`, `CATTITLE`, `SEARCH_ACTION_URL` |

**Важно:** хуки объявляются **в самих файлах-обработчиках** через блок `[BEGIN_COT_EXT]`, а не в setup-файле. Setup содержит только метаданные (`Code`, `Name`, `Version` и т.д.).

### 2.4. Как работает фильтр по категориям

1. Пользователь выбирает категорию в выпадающем списке → `?c=cat_code`.
2. В хуке `page.admin.first` значение `$pxt_c = cot_import('c', 'G', 'ALP')` извлекается и валидируется.
3. Из базового `$sqlwhere` удаляется стандартная конструкция `AND page_cat IN (...)` через `preg_replace`.
4. Вместо неё подставляется фильтр по категории и её подкатегориям через `cot_structure_children('page', $pxt_c)`.
5. Если категория не выбрана — используется стандартное поведение Cotonti.

### 2.5. Как работает поиск

1. Пользователь вводит строку в поле «Поиск» → `?sq=слово`.
2. Строка разбивается по пробелам (`preg_split('/\s+/u', ...)`).
3. Каждое слово экранируется через `Cot::$db-&gt;prep()`.
4. Собирается LIKE-паттерн: `%слово1%слово2%`.
5. Условие добавляется в `$sqlwhere`:
   ```sql
   AND (page_title LIKE '%слово1%слово2%' OR page_text LIKE '%слово1%слово2%')
   ```
6. Поиск работает с несколькими словами и по кириллице (UTF-8).

### 2.6. Параметры URL

| Параметр | Значение | Описание |
|----------|----------|----------|
| `m`      | `page`   | модуль (стандартный) |
| `sq`     | строка   | поисковый запрос |
| `c`      | код      | код категории |
| `filter` | код      | стандартный фильтр Cotonti (all/valqueue/validated/drafts/expired) |
| `sorttype` | код    | поле сортировки |
| `sortway`  | код    | направление (asc/desc) |
| `d`      | число    | номер страницы пагинации |

Все фильтры сохраняются в URL, поэтому ими можно делиться и они работают при переходах по страницам.

### 2.7. Безопасность

- **`cot_import('c', 'G', 'ALP')`** — категория принимается только как буквенно-цифровая строка (ALP), что исключает SQL-инъекции и XSS.
- **`cot_import('sq', 'G', 'TXT')`** — поисковая строка очищается штатным импортёром Cotonti.
- **`Cot::$db-&gt;prep()`** — экранирует спецсимволы перед подстановкой в LIKE.
- **`htmlspecialchars()`** — применяется ко всем пользовательским данным перед вставкой в HTML.
- **Права доступа** — плагин работает только для администраторов Cotonti с доступом к модулю Page. Проверка прав выполняется ядром.
- **CSRF** — стандартные действия (validate, delete, update_checked) защищены штатным `cot_check_xg()` / `cot_check_xp()`, которые плагин не трогает.

---

## 3. Пошаговая установка

### 3.1. 

**Скачать** архив с [GitHub](https://github.com/webitproff/pagextools-cotonti).

### 3.2. 

1. Сделать бекап сайта и базы данных.
2. Распаковать локально на ПК скачанный архив.
3. Загрузить папку `pagextools/` в `/plugins/` вашего Cotonti-сайта.
4. Убедиться, что структура такая:
   ```
   /plugins/pagextools/pagextools.setup.php
   /plugins/pagextools/pagextools.page.admin.first.php
   /plugins/pagextools/pagextools.page.admin.tags.php
   /plugins/pagextools/inc/pagextools.functions.php
   /plugins/pagextools/lang/pagextools.ru.lang.php
   /plugins/pagextools/lang/pagextools.en.lang.php
   ```
5. Войти в админку Cotonti.
6. Перейти в **Расширения → Управление плагинами**.
7. Найти **Page XTools** `pagextools` в списке и нажать **«Установить»**.


### 3.3. Добавление кода в шаблон

**Важно:** плагин добавляет теги в шаблон, но не перезаписывает его.   
Нужно самостоятельно вставить HTML-блок в свой `page.admin.tpl` темы админки.  

НАЙТИ ДЕФОЛТНЫЙ ШАБЛОН modules/page/tpl/page.admin.tpl И ПЕРЕМЕСТИТЬ ЕГО В ПАПКУ ТЕМЫ АДМИНКИ, НАПРИМЕР
ВОТ ТАК themes/admin/cotcp/modules/page/page.admin.tpl 

В ДЕФОЛТНОМ ШАБЛОНЕ НАЙТИ БЛОК:  
```
<!-- ---------------------------------------------------------------------- -->
	<!-- IF {ADMIN_PAGE_TOTALDBPAGES} -->
	<form name="form_valqueue" method="get" action="{PHP|cot_url('admin', 'm=page')}">
		<!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
		<input type="hidden" name="m" value="page" />
		<!-- ENDIF -->
		<table class="cells">
			<thead>
				<tr>
					<th class="coltop w-5"></th>
					<th class="coltop w-5"></th>
					<th class="coltop w-15 filterSelect">{ADMIN_PAGE_FILTER}</th>
					<th class="coltop w-60">
						<!-- IF {TOTAL_ENTRIES} > 1 -->
						{PHP.L.adm_sort} {ADMIN_PAGE_ORDER} {ADMIN_PAGE_WAY}
						<!-- ENDIF -->
					</th>
					<td class="coltop"><button type="submit">{PHP.L.Filter}</button></td>
				</tr>
			</thead>
		</table>
	</form>
	<!-- ENDIF -->
<!-- ---------------------------------------------------------------------- -->	
```
И ПОЛНОСТЬЮ ЕГО ЗАМЕНИТЬ НА БЛОК НИЖЕ:
```
<!-- ---------------------------------------------------------------------- -->

<!-- IF {CATTITLE} -->
<div class="alert alert-info my-2" role="alert">
  {PHP.L.Category}: {CATTITLE}
</div>
<!-- ENDIF -->
<div class="card filter-section p-3 mb-4" style="border: 5px var(--bs-dark-border-subtle) solid">
  <!-- IF {ADMIN_PAGE_TOTALDBPAGES} -->
  <form name="form_valqueue" method="get" action="{SEARCH_ACTION_URL}" class="mb-3">
    <!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
    <input type="hidden" name="m" value="page" />
    <!-- ENDIF -->
    <div class="row g-2 align-items-end">
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.Search}</label>
        <div class="flex-grow-1">{SEARCH_SQ}</div>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.Category}</label>
        <div class="flex-grow-1 filterSelect">{SEARCH_CAT_SELECT2_CAT_EMPTY}</div>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.pagextools_filter_option_status}</label>
        <div class="flex-grow-1">{ADMIN_PAGE_FILTER}</div>
      </div>
      <!-- IF {TOTAL_ENTRIES} > 1 -->
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.pagextools_filter_sorting_fields}</label>
        <div class="flex-grow-1">{ADMIN_PAGE_ORDER}</div>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <label class="form-label">{PHP.L.pagextools_filter_sorting_direction}</label>
        <div class="flex-grow-1">{ADMIN_PAGE_WAY}</div>
      </div>
      <!-- ENDIF -->
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
        <button type="submit" class="btn btn-outline-primary w-100 mt-auto">
          <i class="fa-solid fa-filter me-1"></i>{PHP.L.pagextools_filter} </button>
      </div>
      <div class="col-12 col-lg-3 d-flex flex-column h-100">
		<a class="btn btn-outline-danger w-100 mt-auto" href="{PHP|cot_url('admin','m=page')}"><i class="fa-solid fa-broom me-1"></i>{PHP.L.pagextools_prune} </a>
      </div>
    </div>
  </form>
  <!-- ENDIF -->
</div>

<!-- ---------------------------------------------------------------------- -->
```
в конце файла добавить 
```
<style scoped>
    .filterSelect select {
        width: 100%;
    }
</style>
```
перед закрывающим шаблон блоком:
```
<!-- END: MAIN -->
```

### 3.4. Проверка работоспособности

1. Открыть **Инструменты → Страницы** (`/admin.php?m=page`).
2. Проверяем, что вверху появился блок с полями:
   - **Поиск**;
   - **Категория** (Select2 или обычный select);
   - **Фильтр** (стандартный Cotonti);
   - **Сортировка** (если записей &gt; 1).
3. Ввести в поле «Поиск» слово, например `пример` — нажать **«Фильтровать»**.
4. Проверяем, что список отфильтровался по `page_title` и `page_text`.
5. Выбрать категорию — проверить, что список фильтруется по ней и подкатегориям.
6. Нажать **«Сбросить»** — фильтры очищаются.

---

## 4. Пошаговое руководство по использованию

### 4.1. Поиск по тексту

1. В поле **«Поиск»** ввести слово или фразу (можно несколько слов).
2. Нажать **«Фильтровать»**.
3. Список сузится до записей, у которых `page_title` **или** `page_text` содержит все введённые слова.

**Примеры:**
- `привет` — найдёт все страницы, где есть «привет».
- `привет мир` — найдёт страницы, где есть **и** «привет», **и** «мир».
- Поиск чувствителен к регистру? **Нет**, MySQL-коллация `utf8mb4_unicode_ci` не различает регистр.

### 4.2. Фильтр по категории

1. Открыть выпадающий список **«Категория»**.
2. Выбрать нужную категорию (в дереве отображаются и подкатегории).
3. Нажать **«Фильтровать»**.
4. Список покажет записи из выбранной категории и всех её подкатегорий.

**Сброс категории:** выбрать пустой вариант (`---`) или нажать **«Сбросить»**.

### 4.3. Стандартный фильтр Cotonti

Поле **«Фильтр»** — стандартный фильтр Cotonti:

| Значение | Что показывает |
|----------|----------------|
| `Все` | все страницы (это дефолт плагина) |
| `В очереди` | `page_state = 1` (pending) |
| `Одобренные` | `page_state = 0` (published) |
| `Истёкшие` | `page_begin &gt; now OR (page_expire &lt;&gt; 0 AND page_expire &lt; now)` |
| `Черновики` | `page_state = 2` (draft) |

**Замечание:** базовый Cotonti использует `valqueue` по умолчанию. Плагин меняет дефолт на `all`, чтобы сразу видеть все записи.

### 4.4. Сортировка

- **«Сортировка»** — поле сортировки (ID, заголовок, дата, дата публикации, автор и т.д.).
- **«Направление»** — по возрастанию / по убыванию.

Оба параметра сохраняются в URL и работают при переходах по страницам.

### 4.5. Сброс фильтров

Кнопка **«Сбросить»** (иконка метлы) — переходит на чистый `/admin.php?m=page` без каких-либо фильтров.

---

## 5. Пошаговое руководство по безопасности

Плагин не вводит новых рисков, но для полной безопасности рекомендуется:

1. **Не давайте доступ к админке Pages не-администраторам.** Плагин работает только для тех, у кого есть доступ к модулю Page. Проверка выполняется ядром Cotonti.
2. **Используйте HTTPS.** Поисковые запросы и выбранные категории передаются в URL, и без HTTPS они видны в открытом виде.
3. **Ограничьте длину поисковых запросов** через настройки PHP (например, `max_input_vars`, `post_max_size`). Плагин не ограничивает длину `sq`, поэтому гигантская строка может лечь в лог MySQL.
4. **Не убирайте вызов `cot_check_xg()` из кода.** Это функция проверки XK-токена (аналог CSRF), а не настройка — включить или отключить её из админки нельзя. Она либо вызывается в файле обработчика (например, в `page.admin.php` перед действиями `validate` / `unvalidate` / `delete` / `update_checked`), либо нет. Если кто-то удалит эту строку из кода, действие станет уязвимо к CSRF. Плагин `pagextools` сам `cot_check_xg()` не трогает.
5. **Регулярно обновляйте Cotonti.** Плагин не правит ядро, поэтому обновление CMS — это всегда безопасно (при условии, что структура ядра не меняется радикально).
6. **Не отключайте `.htaccess` и `index.php`** в админ-папке. Это базовая гигиена Cotonti, но стоит упомянуть.
7. **Проверяйте шаблон до и после обновления темы админки.** Если тема обновится — ваш добавленный блок в `page.admin.tpl` может исчезнуть. Это не риск для безопасности, но потребует повторной правки шаблона. **Всегда спасают бекапы, если вы их делали.**

---

## 6. Возможные проблемы и их решения

### 6.1. Форма поиска не появилась

**Симптом:** открываю `/admin.php?m=page` — формы нет.

**Причины:**
- **Плагин не установлен** — проверить в **Расширения → Управление плагинами**.
- **Плагин не активирован** — включить.
- **Не добавлен код в `page.admin.tpl`** — проверить шаг 3.3.
- **Кэш темы/шаблонов** — очистить кэш Cotonti в админке.

### 6.2. Ошибка «Call to undefined function cot_pagextools_selectcat_select2()»

**Причина:** не загружен файл функций.

**Решение:** проверить, что файл `plugins/pagextools/inc/pagextools.functions.php` существует и в хуках есть строка:

```php
require_once cot_incfile('pagextools', 'plug');
```

### 6.3. Ошибка SQL: «Unknown column ...»

**Причина:** используется нестандартное поле `page_*` или шаблон таблицы был изменён.

**Решение:**
- Проверить структуру таблицы `cot_pages` (`SHOW COLUMNS FROM cot_pages`).
- Поиск в плагине работает по `page_title` и `page_text`. Если их нет — плагин некорректен.

### 6.4. Select2 не работает

**Симптом:** селект категории выглядит как обычный ``.

**Причины:**
- Тема админки без Select2 — плагин откатывается на `cot_pagextools_selectcat()` (обычный select). Это **нормальное поведение**, не баг.
- Select2 подключён, но инициализация JS не сработала — проверить консоль браузера.

**Решение:**
- Установить тему **[CotCP](https://github.com/webitproff/cotcp)** — она уже включает совместимость шаблона админки модуля страниц с Select2.
- Или добавить инициализацию Select2 в свой шаблон админки.

### 6.5. Поиск не находит ничего

**Причина:** ищется по `page_title` и `page_text`. Если искомая фраза в `page_desc` или мета-полях — поиск её не найдёт.

**Решение:**
- Расширить поиск, добавив в `$sqlwhere` поля `page_desc` и/или `page_keywords`. Это делается в `pagextools.page.admin.first.php`:

```php
$sqlwhere .= " AND (page_title LIKE '...' OR page_text LIKE '...' OR page_desc LIKE '...')";
```

- Или использовать штатный поиск Cotonti (`/index.php?e=search`).

### 6.6. Поиск не находит кириллицу

**Причина:** база данных в `latin1` или соединение не в `utf8mb4`.

**Решение:**
- Проверить кодировку БД: `SHOW CREATE DATABASE your_db;` — должна быть `utf8mb4`.
- Проверить `datas/config.php` → `$cfg['mysqlcharset']` — должно быть `utf8mb4`.

### 6.7. Фильтр по категории не работает

**Причина:** код категории содержит спецсимволы или её нет в структуре.

**Решение:**
- Проверить, что категория существует: `Cot::$structure['page'][$code]`.
- Проверить, что код категории — буквенно-цифровой (без пробелов, дефисов).

### 6.8. Форма исчезла после обновления темы админки

**Причина:** обновление темы перезаписало `page.admin.tpl`.

**Решение:**
- Повторно вставить код из шага 3.3.



---

## 7. Логи и диагностика

### 7.1. Где смотреть

| Что | Где |
|-----|-----|
| Ошибки PHP | `/var/log/php/error.log`, `error_log` сайта |
| Ошибки MySQL | `/var/log/mysql/error.log` |
| Ошибки Apache | `/var/log/apache2/error.log` |
| Ошибки Nginx | `/var/log/nginx/error.log` |
| Cotonti debug | `datas/config.php` → `$cfg['debug_mode'] = true` |

### 7.2. Включение подробного логирования

В `datas/config.php`:

```php
$cfg['debug_mode'] = true;
$cfg['devmode']    = true;
```
```
@SEE 
system/footer.php  
if (Cot::$cfg['devmode'] && cot_auth('admin', 'a', 'A')) {  
```

После этого все SQL-запросы (включая запросы плагина) будут видны в дебаг области Cotonti с временем выполнения и stack trace.

**Внимание:** не оставляйте эти флаги на production.

---

## 8. FAQ — часто задаваемые вопросы

**Q: Плагин работает без Select2?**
A: Да. Если Select2 не подключён, используется обычный `` через `cot_pagextools_selectcat()`.

**Q: Поиск чувствителен к регистру?**
A: Нет. Коллация `utf8mb4_unicode_ci` в MySQL/MariaDB не различает регистр.

**Q: Поиск работает по описанию (`page_desc`)?**
A: Нет, по умолчанию только по `page_title` и `page_text`. Расширяется правкой хука `page.admin.first`.

**Q: Можно ли искать по мета-полям (`page_keywords`, `page_metatitle`)?**
A: Штатно — нет. Требуется правка хука.

**Q: Работает ли на Cotonti Verona 1.0.x?**
A: Да, начиная с версии 1.0.0 плагина. Используется `Cot::$db`, `Cot::$structure` — совместимо.

**Q: Плагин совместим с Cotonti Siena 0.9.x?**
A: Да, начиная с 1.0.0.

**Q: Нужно ли править ядро?**
A: Нет. Всё через хуки. При обновлении Cotonti плагин продолжит работать.

**Q: Что будет, если я отключу плагин?**
A: Форма поиска и фильтра перестанут отображаться, остальное поведение админки вернётся к штатному. Никаких данных в БД плагин не хранит.

**Q: Можно ли добавить сортировку по дате?**
A: Да, это штатная возможность Cotonti — поле `sorttype` в форме уже содержит все поля, включая `date`.

**Q: Поиск тормозит на большой базе?**
A: Да, если в `page_title` и `page_text` нет индексов. Для больших таблиц (100k+ записей) добавьте FULLTEXT-индекс:

```sql
ALTER TABLE cot_pages ADD FULLTEXT(page_title, page_text);
```

и используйте `MATCH ... AGAINST` вместо `LIKE`. Это требует правки хука.

**Q: Поддерживает ли плагин мультиязычность?**
A: Да, есть `pagextools.ru.lang.php` и `pagextools.en.lang.php`.

**Q: Можно ли использовать плагин на нескольких сайтах?**
A: Да. Он не привязан к домену.

---

## 9. Обновление плагина

1. **Сделать бэкап** файлов плагина и (на всякий случай) базы данных.
2. Скачать новую версию из маркетплейса или с GitHub.
3. В админке Cotonti → **Расширения → Управление плагинами → Page XTools → Обновить**.
4. Если обновление через админку недоступно — распаковать поверх старой папки `plugins/pagextools/`.
5. Проверить, что файлы `pagextools.page.admin.first.php` и `pagextools.page.admin.tags.php` не потеряли блок `[BEGIN_COT_EXT]` с хуками.
6. Открыть `/admin.php?m=page` и проверить работоспособность.

---

## 10. Удаление плагина

1. В админке Cotonti → **Расширения → Управление плагинами → Page XTools → Удалить**.
2. Удалить папку `plugins/pagextools/` вручную, если она осталась.
3. **Удалить блок формы** из `themes/admin/{тема}/modules/page/page.admin.tpl` (иначе останутся «висячие» теги `{SEARCH_SQ}` — Cotonti их просто проигнорирует, но чистота важна).
4. Удалить `` в конце шаблона.
5. Очистить кэш Cotonti.

**Важно:** плагин не хранит данные в БД, поэтому удаление безопасно.

---

## 11. Дорожная карта

- [ ] **Расширение поиска** — включить `page_desc`, `page_keywords`, `page_metatitle`.
- [ ] **FULLTEXT-индекс** — опциональная настройка через конфиг.
- [ ] **Мультифильтр по нескольким категориям** — множественный выбор.
- [ ] **Сохранение фильтров в cookie** — чтобы при открытии админки фильтры восстанавливались.
- [ ] **Диапазон дат** — фильтр по `page_date` / `page_begin` / `page_expire`.
- [ ] **Автор** — фильтр по `page_ownerid`.
- [ ] **Экспорт списка** — выгрузка отфильтрованного списка в CSV.
- [ ] **Поддержка Postgres** — при переходе Cotonti на другие СУБД.
- [ ] **Расширенная сортировка** — несколько полей.
- [ ] **Поиск по extrafields** — по настраиваемым полям.

---

## 12. История изменений

### v1.0.0 (2026-10-17)

- Первая публичная версия.
- Хуки `page.admin.first` и `page.admin.tags`.
- Поиск по `page_title` и `page_text` с поддержкой нескольких слов.
- Фильтр по категории с подкатегориями.
- Select2-совместимый селект + fallback.
- Дефолт фильтра изменён с `valqueue` на `all`.
- Локализация `ru` / `en`.
- Метаданные в setup-файле, хуки — в отдельных файлах.

---

## 13. Участие в разработке

Pull-request'ы приветствуются. Перед отправкой:

1. Форкните [репозиторий](https://github.com/webitproff/pagextools-cotonti).
2. Создайте ветку `feature/your-feature` или `fix/your-bug`.
3. Следуйте PSR-12.
4. Добавьте тесты, если возможно.
5. Обновите `README.md` и `CHANGELOG`.
6. Отправьте PR (Pull-request) с описанием: что, зачем, как проверить.

**Правила для кода:**
- Имя функций — с префиксом `cot_pagextools_`.
- Никаких правок ядра.
- Все SQL-параметры — через `Cot::$db-&gt;prep()` или биндинг.
- Все пользовательские данные в HTML — через `htmlspecialchars()`.
- Мультиязычность — через `$L['pagextools_*']`.

---

## 14. Лицензия

BSD 3-Clause. Полный текст — в файле `License.txt`.

---

## 15. Контакты и поддержка

- **Маркетплейс Cotonti:** [abuyfile.com/ru/market/cotonti/plugs/pagextools](https://abuyfile.com/ru/market/cotonti/plugs/pagextools)
- **Репозиторий GitHub:** [github.com/webitproff/pagextools-cotonti](https://github.com/webitproff/pagextools-cotonti)
- **Форум поддержки:** [abuyfile.com/ru/forums/cotonti/custom/plugs](https://abuyfile.com/ru/forums/cotonti/custom/plugs)
- **Автор:** [webitproff](https://abuyfile.com/ru/users/webitproff)
- **Issues:** [github.com/webitproff/pagextools-cotonti/issues](https://github.com/webitproff/pagextools-cotonti/issues)

  