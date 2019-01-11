@extends('admin::voyager.master')

@section('page_title', __('voyager::generic.view').' '.$dataTypeContent->name."'s Profile")

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title" style="padding-left: 50px;">
            <i class="{{ $dataType->icon }}" style="left: 0;"></i> {{ ucfirst($dataType->display_name_singular) }}
        </h1>
        <div class="pull-right" style="margin-top:30px;">
            @can('edit', $dataTypeContent)
                <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                    <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                    {{ __('voyager::generic.edit') }}
                </a>
            @endcan
            @can('delete', $dataTypeContent)
                <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
                </a>
            @endcan
            <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
                <span class="glyphicon glyphicon-list"></span>&nbsp;
                {{ __('voyager::generic.return_to_list') }}
            </a>
        </div>
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-body" style="padding-top: 50px;">
                        <div class="text-center">
                            @php
                                $imageParams = ["template" => "dynamic", "params" => ["w" => 120, "h" => 120]];
                                $allowedFields = ["email", "created_at", "locale", "user_belongsto_role_relationship"]
                            @endphp
                            <img src="{{ filter_var($dataTypeContent->avatar, FILTER_VALIDATE_URL) ? $dataTypeContent->avatar : Voyager::image($dataTypeContent->avatar, "", $imageParams) }}" style="width:120px;height:120px;" class="avatar img-circle" alt="Admin avatar">
                            <h3 class="panel-title text-center">{{$dataTypeContent->name}}</h3>
                        </div>
                        <div class="">
                            <ul class="list-unstyled list-separated">
                                @foreach($dataType->readRows as $row)
                                    @if(in_array($row->field, $allowedFields))
                                        <li class="list-separated-item">
                                            <div class="row">
                                                <div class="col-xs-5 text-muted">
                                                    {{ $row->display_name }} 
                                                </div>
                                                <div class="col-xs-7 text-right">
                                                    @if($row->field == 'user_belongsto_role_relationship')
                                                        <b>{{$dataTypeContent->role->display_name }}</b>
                                                    @elseif($row->field == 'user_belongstomany_role_relationship')
                                                        <b>{{$dataTypeContent->roles }}</b>
                                                    @else
                                                        <b>{{$dataTypeContent->{$row->field} }}</b>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <!-- ### TITLE ### -->
                <div class="panel">
                    <div class="panel-body">
                        <p style="border-radius:4px; padding:20px; background:#fff; margin:0; color:#999; text-align:center;">
                            Add extra content here
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->display_name_singular) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>
        <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) {
                // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');

            $('#delete_modal').modal('show');
        });

    </script>
@stop
