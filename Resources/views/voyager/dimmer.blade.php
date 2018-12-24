<div class="panel widget center bgimage" style="padding: 10px; margin-bottom:0;overflow:hidden;background-image:url('{{ $image }}');">
    <div class="dimmer" style="background: linear-gradient(45deg,rgba(45,53,61,.79) 0,rgba(45,53,61,.2) 100%);"></div>
    <div class="panel-content">
        <div class="row" style="padding-left: 0; padding-right: 0;">
            <a href="{{ $button['link'] }}" >
                <div class="col-xs-3" style="margin-bottom: 0;">
                    @if (isset($icon))<i class='{{ $icon }}' style="margin: 0; background:none; font-size: 64px;"></i>@endif
                </div>
                <div class="col-xs-9" style="margin-bottom: 0;">
                    <h4 style="font-weight: 400; font-size: 30px; margin: 1em 0;" class="text-right">{!! $title !!}</h4>
                </div>
            </a>
        </div>
    </div>
</div>