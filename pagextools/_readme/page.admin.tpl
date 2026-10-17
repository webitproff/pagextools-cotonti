<!-- BEGIN: MAIN -->
<div class="mb-3">
  <div class="row g-2">
    <div class="col-12 col-lg-3">
      <a href="{ADMIN_PAGE_URL_CONFIG}" class="btn btn-outline-secondary w-100">
        <i class="fa-solid fa-gear me-1"></i>{PHP.L.Configuration} </a>
    </div>
    <div class="col-12 col-lg-3">
      <a href="{ADMIN_PAGE_URL_STRUCTURE}" class="btn btn-outline-secondary w-100">
        <i class="fa-solid fa-list-ul me-1"></i>{PHP.L.Categories} </a>
    </div>
    <div class="col-12 col-lg-3">
      <a href="{ADMIN_PAGE_URL_EXTRAFIELDS}" class="btn btn-outline-secondary w-100">
        <i class="fa-solid fa-table-columns me-1"></i>{PHP.L.Extrafields} </a>
    </div>
    <div class="col-12 col-lg-3">
      <a href="{ADMIN_PAGE_URL_ADD}" class="btn btn-outline-primary w-100">
        <i class="fa-solid fa-plus me-1"></i>{PHP.L.page_addtitle} </a>
    </div>
  </div>
</div> 
{FILE "{PHP.cfg.themes_dir}/admin/{PHP.cfg.admintheme}/warnings.tpl"}
<hr>
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
        <div class="flex-grow-1 filterSelect">{SEARCH_CAT_SELECT2}</div>
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
<div class="mb-3">
  <form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_PAGE_FORM_URL}">
    <div class="list-group list-group-flush">
      <div class="list-group-item list-group-item-dark">
        <div class="row align-items-center fw-bold">
          <div class="col-1"></div>
          <div class="col-1">{PHP.L.Id}</div>
          <div class="col-md-2">{PHP.L.Status}</div>
          <div class="col-md-5">{PHP.L.Title}</div>
          <div class="col-md-3">{PHP.L.Action}</div>
        </div>
      </div>
      <!-- BEGIN: PAGE_ROW -->
      <div class="list-group-item list-group-item-action">
        <div class="row align-items-center">
          <div class="col-1 text-center">
            <input type="checkbox" name="s[{ADMIN_PAGE_ID}]" class="form-check-input checkbox" />
          </div>
          <div class="col-1">{ADMIN_PAGE_ID}</div>
          <div class="col-md-2">{ADMIN_PAGE_LOCAL_STATUS}</div>
          <div class="col-md-5">
            <div id="mor_{PHP.ii}" class="mor_info_on_off overflow-x-auto" style="max-width: 675px;">
              <span class="fw-bold" style="cursor: pointer;">{ADMIN_PAGE_TITLE}</span>
              <!-- IF {ADMIN_PAGE_DESCRIPTION} -->
              <div class="text-muted small">{ADMIN_PAGE_DESCRIPTION}</div>
              <!-- ENDIF -->
              <div class="moreinfo collapse">
                <hr class="my-2" />
                <strong>{ADMIN_PAGE_UPDATED}</strong> 
                <!-- IF {ADMIN_PAGE_TEXT} -->
                <div class="mt-2">
                  <strong>{PHP.L.Text}:</strong>
                  <div>{ADMIN_PAGE_TEXT}</div>
                </div>
                <!-- ENDIF -->
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="d-flex flex-wrap gap-1">
              <!-- IF {PHP.row.page_state} == 1 -->
              <a href="{ADMIN_PAGE_URL_FOR_VALIDATED}" class="btn btn-sm btn-outline-success confirmLink">
                <i class="fa-solid fa-check me-1"></i>{PHP.L.Validate} </a>
              <!-- ENDIF -->
              <a href="{ADMIN_PAGE_URL_FOR_DELETED}" class="btn btn-sm btn-outline-danger confirmLink">
                <i class="fa-solid fa-trash me-1"></i>{PHP.L.Delete} </a>
              <a href="{ADMIN_PAGE_ID_URL}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-eye me-1"></i>{PHP.L.Open} </a>
              <a href="{ADMIN_PAGE_URL_FOR_EDIT}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-pen me-1"></i>{PHP.L.Edit} </a>
            </div>
          </div>
        </div>
      </div>
      <!-- END: PAGE_ROW -->
      <!-- IF !{TOTAL_ENTRIES} -->
      <div class="list-group-item text-center">{PHP.L.None}</div>
      <!-- ELSE -->
      <div class="list-group-item mt-4">
        <div class="d-flex gap-2">
          <!-- IF {PHP.filter} != 'validated' -->
          <button name="paction" type="submit" value="validate" class="btn btn-success confirm">
            <i class="fa-solid fa-check me-1"></i>{PHP.L.Validate} </button>
          <!-- ENDIF -->
          <button name="paction" type="submit" value="delete" class="btn btn-danger confirm">
            <i class="fa-solid fa-trash me-1"></i>{PHP.L.Delete} </button>
        </div>
      </div>
      <!-- ENDIF -->
    </div>
  </form>
</div>
<!-- IF {TOTAL_ENTRIES} -->
<nav aria-label="Page Pagination" class="mt-3">
  <div class="text-center mb-2">{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</div>
  <ul class="pagination justify-content-center">{PREVIOUS_PAGE} {PAGINATION} {NEXT_PAGE}</ul>
</nav>
<!-- ENDIF -->

<style scoped>
    .filterSelect select {
        width: 100%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        $('.moreinfo').hide();
        $('.mor_info_on_off').click(function () {
            let $this = $(this);
            $this.find('.moreinfo').slideToggle(100);
        });

        let submitButtons = document.querySelectorAll('.confirm');
        let form = document.getElementById('form_valqueue');
        submitButtons.forEach(function (elem) {
            elem.addEventListener('click', function (e) {
                let checkedCnt = form.querySelectorAll('input[type=checkbox]:checked').length;
                if (checkedCnt < 1) {
                    e.preventDefault();
                    return false;
                }

                let message = 'Are you sure?';
                switch (this.value) {
                    case 'delete':
                        message = '{PHP.L.page_confirm_delete}';
                        break;
                    case 'validate':
                        message = '{PHP.L.page_confirm_validate}';
                        break;
                }

                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
<!-- IF {PHP|function_exists('cot_debug_tpl_url')} -->
<div class="alert alert-secondary" role="alert">
	<div class="fw-semibold mb-2">{PHP.L.cotcp_path_current_tpl}</div>
	<div class="text-danger fw-semibold">{PHP|cot_debug_tpl_url()}</div>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->