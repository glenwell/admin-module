<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="icon-search-stats-1"></i> {{ __('SEO Analysis') }}
        </h3>
        <div class="panel-actions">
            <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        <div id="accordion">

        </div>
        <input type="hidden" name="seo_score" id="seo_score" value="@if(isset($dataTypeContent->seo_score)){{ $dataTypeContent->seo_score }}@endif">
    </div>
</div>

