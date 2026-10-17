НАЙТИ ДЕФОЛТНЫЙ ШАБЛОН modules/page/tpl/page.admin.tpl И ПЕРЕМЕСТИТЬ ЕГО В ПАПКУ ТЕМЫ АДМИНКИ, НАПРИМЕР
ВОТ ТАК themes/admin/cotcp/modules/page/page.admin.tpl 

В ДЕФОЛТНОМ ШАБЛОНЕ НАЙТИ БЛОК
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

И ПОЛНОСТЬЮ ЕГО ЗАМЕНИТЬ НА БЛОК НИЖЕ:
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

в конце файла добавить 

<style scoped>
    .filterSelect select {
        width: 100%;
    }
</style>

перед закрывающим шаблон блоком:
<!-- END: MAIN -->




