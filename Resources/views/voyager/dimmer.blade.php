<div class="panel widget center bgimage custom-dimmer" style="background-image:url('{{ $image }}');">
    <div class="dimmer"></div>
    <div class="panel-content">
        <div class="row" style="">
            <a href="{{ $button['link'] }}" class="custom-dimmer-link" >
                <div class="col-xs-3">
                    @if (isset($icon))<i class='{{ $icon }}'></i>@endif
                </div>
                <div class="col-xs-9">
                    <h4 class="text-right">{!! $title !!}</h4>
                </div>
            </a>
        </div>
    </div>
</div>