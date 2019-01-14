@extends('admin::voyager.master')

@section('page_title', __('voyager::generic.view').' '.$dataType->display_name_singular)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title" style="padding-left: 50px;">
            <i class="{{ $dataType->icon }}" style="left: 0;"></i> {{ title_case($dataTypeContent->title) }}
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
            <div class="col-md-9">
                <div class="panel">
                    <div class="panel-body" style="padding:0">
                        @php
                            $imageParams = ["template" => "dynamic", "params" => ["w" => 1280, "h" => 500]]
                        @endphp
                        <img class="img-responsive img-rounded" src="{{ filter_var($dataTypeContent->image, FILTER_VALIDATE_URL) ? $dataTypeContent->image : Voyager::image($dataTypeContent->image, "", $imageParams) }}" alt="">
                        <div class="post-container">
                            @if (!empty($dataTypeContent->excerpt))
                                <blockquote>{{$dataTypeContent->excerpt}}</blockquote>
                            @endif
                            
                            <div class="post-body">
                                {!!$dataTypeContent->body!!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="icon-link-2"></i> {{ __('Page Details') }}
                        </h3>
                        <div class="panel-actions">
                            <a title="Open page in new page" class="panel-action icon-expand-4"></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled list-separated">
                            @php
                                $pageDetails = [
                                    [
                                        "name" => "Author",
                                        "data" => isset($dataTypeContent->authorId->name) ? $dataTypeContent->authorId->name : "None",
                                        "class" => null
                                    ],
                                    /* [
                                        "name" => "Category",
                                        "data" => isset($dataTypeContent->category->name) ? $dataTypeContent->category->name : "None",
                                        "class" => null
                                    ], */
                                    [
                                        "name" => "Status",
                                        "data" => $dataTypeContent->status,
                                        "class" => $dataTypeContent->status == "ACTIVE" ? "text-success" : "text-danger"
                                    ],
                                    /* [
                                        "name" => "Featured",
                                        "data" => $dataTypeContent->featured ? "YES" : "NO",
                                        "class" => null
                                    ], */
                                    [
                                        "name" => "Created",
                                        "data" => \Carbon\Carbon::parse($dataTypeContent->created_at)->format('M d, Y H:i'),
                                        "class" => null
                                    ],
                                    [
                                        "name" => "Updated",
                                        "data" => \Carbon\Carbon::parse($dataTypeContent->updated_at)->format('M d, Y H:i'),
                                        "class" => null
                                    ],
                                ];

                                $seoDetails = [
                                    /* [
                                        "name" => "SEO Title",
                                        "data" => $dataTypeContent->seo_title,
                                        "class" => null
                                    ], */
                                    [
                                        "name" => "Slug",
                                        "data" => $dataTypeContent->slug,
                                        "class" => null
                                    ],
                                    [
                                        "name" => "Meta Description",
                                        "data" => $dataTypeContent->meta_description,
                                        "class" => $dataTypeContent->status == "PUBLISHED" ? "text-success" : "text-danger"
                                    ],
                                    [
                                        "name" => "Focus Keywords",
                                        "data" => $dataTypeContent->focus_keywords,
                                        "class" => null
                                    ],
                                ];
                            @endphp

                            @foreach ($pageDetails as $detail)
                                <li>
                                    <div class="row">
                                        <div class="col-xs-3 post-key">{{$detail["name"]}}</div>
                                        <div class="col-xs-9 text-right">
                                            <strong class="{{$detail["class"]}}">{{$detail["data"]}}</strong>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="icon-internet"></i> {{ __('SEO Details') }}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled list-separated">
                            @foreach ($seoDetails as $detail)
                                <li>
                                    <div class="row">
                                        <div class="col-xs-12 post-key">{{$detail["name"]}}</div>
                                        <div class="col-xs-12">
                                            <strong>{{$detail["data"]}}</strong>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
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
