<!--
	* Page XTools — stats widget for the admin home mainpanel
	*
	* Filename: _ _ _ _ _ _ _ pagextools.admin.home.tpl
	* Base path:_ _ _ _ _ _ _ plugins/pagextools/tpl/pagextools.admin.home.tpl
	* Recommended: _ _ _ _ _  themes/admin/{admintheme}/plugins/pagextools/pagextools.admin.home.tpl
	* Business logic: _ _ _ _ _ _ _ plugins/pagextools/pagextools.admin.home.php
	*
	* Hook:  admin.home.mainpanel  (Order=10)
	*
	* @package pagextools
	* @version 1.0.1
	* @author webitproff
	* @license BSD-3-Clause
-->

<!-- BEGIN: MAIN -->

<div class="card border-0 shadow-sm mb-3 pagextools-admin-home">
    <div class="card-header bg-primary bg-gradient text-white d-flex align-items-center justify-content-between py-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-file-lines me-2"></i>
            <strong>{PHP.L.Pages}</strong>
        </div>
        <a href="{ADMIN_HOME_URL}" class="btn btn-sm btn-light py-0 px-2" title="{PHP.L.Administration}">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="card-body py-3">

        <!-- IF {ADMIN_HOME_HAS_PENDING} -->
        <a href="{ADMIN_HOME_QUEUE_URL}"
           class="alert alert-warning d-flex align-items-center text-decoration-none mb-3 py-2 px-2 small">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>
            <span class="flex-grow-1">{PHP.L.page_status_pending}</span>
            <span class="badge bg-warning text-dark">{ADMIN_HOME_PENDING}</span>
        </a>
        <!-- ENDIF -->

        <!-- IF {ADMIN_HOME_IS_EMPTY} -->
        <div class="text-center text-muted small py-3">
            <i class="fa-solid fa-file-circle-xmark fa-2x mb-2 d-block opacity-50"></i>
            {PHP.L.pagextools_stats_no_pages}
        </div>
        <!-- ENDIF -->

        <!-- IF {ADMIN_HOME_HAS_ITEMS} -->
        <div class="alert alert-warning mb-3" role="alert">
            <div class="text-muted small fst-italic">{PHP.L.Total} {ADMIN_HOME_TOTAL}</div>
        </div>
        <!-- ENDIF -->

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-success bg-opacity-10">
                    <div class="text-success fw-bold fs-5 lh-1">{ADMIN_HOME_PUBLISHED}</div>
                    <div class="mt-1">{PHP.L.page_status_published}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-warning bg-opacity-10">
                    <div class="text-warning fw-bold fs-5 lh-1">{ADMIN_HOME_PENDING}</div>
                    <div class="mt-1">{PHP.L.page_status_pending}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-secondary bg-opacity-10">
                    <div class="text-secondary fw-bold fs-5 lh-1">{ADMIN_HOME_DRAFTS}</div>
                    <div class="mt-1">{PHP.L.page_status_draft}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-primary bg-opacity-10">
                    <div class="text-primary fw-bold fs-5 lh-1">{ADMIN_HOME_TOTAL}</div>
                    <div class="mt-1">{PHP.L.Total}</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span><i class="fa-solid fa-circle-check text-success me-1"></i>{PHP.L.page_status_published}</span>
                <span class="fw-semibold">{ADMIN_HOME_PUBLISHED_PCT}%</span>
            </div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-success" style="width:{ADMIN_HOME_PUBLISHED_PCT}%"></div>
            </div>
        </div>

    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="card border-0 shadow-sm mb-3 pagextools-admin-home">
            <div class="card-body py-3">
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1">
                    <div class="fw-semibold text-muted text-uppercase mx-2">
                        <i class="fa-solid fa-layer-group me-1"></i>{PHP.L.pagextools_stats_top_cats}
                    </div>
                    <hr class="flex-grow-1">
                </div>
                <!-- BEGIN: TOP_CATS -->
                <div class="mb-3">
                    <!-- BEGIN: TOP_CAT_ROW -->
                    <a href="{TOP_CAT_URL}"
                       class="d-flex justify-content-between align-items-center text-decoration-none text-body py-1 top-cat-row">
                        <span class="text-truncate me-2">
                            <i class="fa-solid fa-folder text-muted me-1"></i>{TOP_CAT_TITLE}
                        </span>
                        <span class="badge bg-secondary bg-opacity-25 text-secondary">{TOP_CAT_COUNT}</span>
                    </a>
                    <!-- END: TOP_CAT_ROW -->
                    <!-- BEGIN: TOP_CATS_EMPTY -->
                    <div class="text-muted small fst-italic">{PHP.L.None}</div>
                    <!-- END: TOP_CATS_EMPTY -->
                </div>
                <!-- END: TOP_CATS -->
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card border-0 shadow-sm mb-3 pagextools-admin-home">
            <div class="card-body py-3">
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1">
                    <div class="fw-semibold text-muted text-uppercase mx-2">
                        <i class="fa-solid fa-chart-line me-1"></i>{PHP.L.pagextools_stats_activity}
                    </div>
                    <hr class="flex-grow-1">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa-solid fa-calendar-day me-1 text-muted"></i>{PHP.L.pagextools_stats_today}</span>
                        <strong>{ADMIN_HOME_TODAY}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa-solid fa-calendar-week me-1 text-muted"></i>{PHP.L.pagextools_stats_week}</span>
                        <strong>{ADMIN_HOME_WEEK}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fa-solid fa-calendar me-1 text-muted"></i>{PHP.L.pagextools_stats_month}</span>
                        <strong>{ADMIN_HOME_MONTH}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fa-solid fa-eye me-1 text-muted"></i>{PHP.L.Hits}</span>
                        <strong>{ADMIN_HOME_VIEWS}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3 pagextools-admin-home">
    <div class="card-body py-3">
        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1">
            <div class="fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-clock-rotate-left me-1"></i>{PHP.L.pagextools_stats_latest}
            </div>
            <hr class="flex-grow-1">
        </div>
        <div class="mb-3">
            <!-- BEGIN: RECENT_ITEMS -->
            <!-- BEGIN: RECENT_ROW -->
            <a href="{RECENT_ITEM_URL}"
               class="d-flex align-items-start text-decoration-none text-body py-1 border-bottom border-light-subtle recent-row">
                <span class="badge bg-{RECENT_ITEM_STATE_CLASS} bg-opacity-25 text-{RECENT_ITEM_STATE_CLASS} me-2 flex-shrink-0">
                    #{RECENT_ITEM_ID}
                </span>
                <span class="flex-grow-1 text-truncate">
                    <span class="d-block text-truncate fw-medium">{RECENT_ITEM_TITLE}</span>
                    <span class="d-block text-muted">
                        <i class="fa-regular fa-folder me-1"></i>{RECENT_ITEM_CAT}
                        · {RECENT_ITEM_DATE}
                    </span>
                </span>
            </a>
            <!-- END: RECENT_ROW -->
            <!-- BEGIN: RECENT_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.None}</div>
            <!-- END: RECENT_EMPTY -->
            <!-- END: RECENT_ITEMS -->
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3 pagextools-admin-home">
    <div class="card-body py-3">
        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1">
            <div class="fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-fire text-danger me-1"></i>{PHP.L.pagextools_stats_top_viewed}
            </div>
            <hr class="flex-grow-1">
        </div>
        <!-- BEGIN: TOP_VIEWED -->
        <div class="mb-3">
            <!-- BEGIN: TOP_VIEWED_ROW -->
            <a href="{TOP_VIEWED_URL}"
               class="d-flex justify-content-between align-items-center text-decoration-none text-body py-1 top-viewed-row">
                <span class="text-truncate me-2">
                    <span class="badge bg-danger bg-opacity-25 text-danger me-1">#{TOP_VIEWED_ID}</span>
                    {TOP_VIEWED_TITLE}
                    <span class="text-muted">· {TOP_VIEWED_CAT}</span>
                </span>
                <span class="badge bg-danger bg-opacity-25 text-danger-emphasis flex-shrink-0">
                    <i class="fa-solid fa-eye me-1"></i>{TOP_VIEWED_COUNT}
                </span>
            </a>
            <!-- END: TOP_VIEWED_ROW -->
            <!-- BEGIN: TOP_VIEWED_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.None}</div>
            <!-- END: TOP_VIEWED_EMPTY -->
        </div>
        <!-- END: TOP_VIEWED -->
    </div>
</div>

<div class="card border-0 shadow-sm mb-3 pagextools-admin-home">
    <div class="card-body py-3">
        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1">
            <div class="fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-user-tie me-1"></i>{PHP.L.pagextools_stats_top_authors}
            </div>
            <hr class="flex-grow-1">
        </div>
        <!-- BEGIN: TOP_AUTHORS -->
        <div class="mb-3">
            <!-- BEGIN: TOP_AUTHOR_ROW -->
            <a href="{TOP_AUTHOR_URL}"
               class="d-flex justify-content-between align-items-center text-decoration-none text-body py-1 top-author-row">
                <span class="fs-5"><i class="fa-solid fa-user me-2"></i>{TOP_AUTHOR_NAME}</span>
                <span class="badge bg-info bg-opacity-25 text-info-emphasis">{TOP_AUTHOR_COUNT}</span>
            </a>
            <!-- END: TOP_AUTHOR_ROW -->
            <!-- BEGIN: TOP_AUTHORS_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.None}</div>
            <!-- END: TOP_AUTHORS_EMPTY -->
        </div>
        <!-- END: TOP_AUTHORS -->
    </div>
</div>

<div class="d-flex align-items-center my-4">
    <hr class="flex-grow-1">
    <div class="small fw-semibold text-muted text-uppercase mx-2">
        <a href="{ADMIN_HOME_URL}" class="btn btn-sm btn-outline-danger">
            <i class="fa-solid fa-screwdriver-wrench me-2"></i>{PHP.L.Administration}
        </a>
    </div>
    <hr class="flex-grow-1">
</div>

<div class="row gy-3 mb-5">
    <div class="col-12 col-lg-3">
        <a href="{ADMIN_HOME_CONFIG_URL}" class="btn btn-outline-secondary w-100">
            <i class="fa-solid fa-gear me-1"></i>{PHP.L.pagextools_admin_config}
        </a>
    </div>
    <div class="col-12 col-lg-3">
        <a href="{ADMIN_HOME_STRUCTURE_URL}" class="btn btn-outline-secondary w-100">
            <i class="fa-solid fa-list-ul me-1"></i>{PHP.L.pagextools_admin_structure}
        </a>
    </div>
    <div class="col-12 col-lg-3">
        <a href="{ADMIN_HOME_EXTRAFIELDS_URL}" class="btn btn-outline-secondary w-100">
            <i class="fa-solid fa-table-columns me-1"></i>{PHP.L.pagextools_admin_extrafields}
        </a>
    </div>
    <div class="col-12 col-lg-3">
        <a href="{ADMIN_HOME_ADD_URL}" class="btn btn-outline-primary w-100" target="_blank">
            <i class="fa-solid fa-plus me-1"></i>{PHP.L.pagextools_admin_add_page}
        </a>
    </div>
</div>

<style>
    .pagextools-admin-home .recent-row:hover,
    .pagextools-admin-home .top-cat-row:hover,
    .pagextools-admin-home .top-author-row:hover,
    .pagextools-admin-home .top-viewed-row:hover {
        background: var(--bs-tertiary-bg);
        border-radius: .25rem;
        padding-left: .25rem;
        padding-right: .25rem;
    }
    .pagextools-admin-home .recent-row:last-child,
    .pagextools-admin-home .top-cat-row:last-child,
    .pagextools-admin-home .top-author-row:last-child,
    .pagextools-admin-home .top-viewed-row:last-child {
        border-bottom: 0 !important;
    }
</style>

<!-- IF {TPL_PATH} -->
<div class="container px-3 px-lg-5 py-5">
    <div class="alert alert-info" role="alert">{TPL_PATH}</div>
</div>

<!-- ENDIF -->

<!-- END: MAIN -->