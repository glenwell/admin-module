@extends('admin::voyager.master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title" style="padding-left: 50px;">
            <i class="{{ $dataType->icon }}" style="left: 0;"></i> 
            {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
        </h1>
        @include('admin::voyager.posts.seo.highlight')
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form" action="@if(isset($dataTypeContent->id)){{ route('voyager.pages.update', $dataTypeContent->id) }}@else{{ route('voyager.pages.store') }}@endif" method="POST" enctype="multipart/form-data">
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-9">
                    <!-- ### TITLE ### -->
                    <div class="panel">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="icon-pencil2"></i> {{ __('Page Title') }}
                            </h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            @include('voyager::multilingual.input-hidden', [
                                '_field_name'  => 'title',
                                '_field_trans' => get_field_translations($dataTypeContent, 'title')
                            ])
                            <input type="text" class="form-control" id="title" data-toggle="tooltip" data-placement="top" title="A title for social media news feeds that captures attention." name="title" placeholder="{{ __('voyager::generic.title') }}" value="@if(isset($dataTypeContent->title)){{ $dataTypeContent->title }}@endif">
                        </div>
                    </div>

                    <!-- ### CONTENT ### -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="icon-newspaper"></i> {{ __('Page Content') }}
                            </h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-resize-full" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                            </div>
                        </div>

                        @include('voyager::multilingual.input-hidden', [
                            '_field_name'  => 'body',
                            '_field_trans' => get_field_translations($dataTypeContent, 'body')
                        ])
                        @php
                            $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};
                            $row = $dataTypeRows->where('field', 'body')->first();
                        @endphp
                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                    </div><!-- .panel -->

                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="icon-plus"></i> {{ __('voyager::post.additional_fields') }}
                            </h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            @php
                                $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};
                                $exclude = ['title', 'body', 'excerpt', 'slug', 'status', 'author_id', 'featured', 'image', 'image_meta', 'meta_description', 'focus_keywords', 'seo_score'];
                            @endphp

                            @foreach($dataTypeRows as $row)
                                @if(!in_array($row->field, $exclude))
                                    @php
                                        $display_options = isset($row->details->display) ? $row->details->display : NULL;
                                    @endphp
                                    @if (isset($row->details->formfields_custom))
                                        @include('voyager::formfields.custom.' . $row->details->formfields_custom)
                                    @else
                                        <div class="form-group @if($row->type == 'hidden') hidden @endif @if(isset($display_options->width)){{ 'col-md-' . $display_options->width }}@endif" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                            {{ $row->slugify }}
                                            <label for="name">{{ $row->display_name }}</label>
                                            @include('voyager::multilingual.input-hidden-bread-edit-add')
                                            @if($row->type == 'relationship')
                                                @include('voyager::formfields.relationship', ['options' => $row->details])
                                            @else
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                            @endif

                                            @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                                {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                    <!-- ### SETTING SELECTOR ### -->
                    <div class="panel">
                        <div class="panel-body" style="padding:0 !important;">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#settings"><i class="icon-settings"></i> <strong>Settings</strong></a></li>
                                <li><a data-toggle="tab" href="#seo"><i class="icon-search-stats-1"></i> <strong>SEO</strong></a></li>
                            </ul>
                        </div>
                        </div>
                    <div class="tab-content">
                        <div id="settings" class="tab-pane custom-pane fade in active">
                            <!-- ### DETAILS ### -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="icon-link-2"></i> {{ __('Page Details') }}
                                    </h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="slug">{{ __('voyager::post.slug') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'slug',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'slug')
                                        ])
                                        <input type="text" class="form-control" id="slug" name="slug"
                                            placeholder="slug" data-toggle="tooltip" data-placement="top" title="The final URL will include this slug for better SEO."
                                            {!! isFieldSlugAutoGenerator($dataType, $dataTypeContent, "slug") !!}
                                            value="@if(isset($dataTypeContent->slug)){{ $dataTypeContent->slug }}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label for="status">{{ __('Page Status') }}</label>
                                        <select class="form-control" name="status" data-toggle="tooltip" data-placement="top" title="Choose whether this page is visible or not.">
                                            <option value="ACTIVE"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'ACTIVE') selected="selected"@endif>{{ __('ACTIVE') }}</option>
                                            <option value="INACTIVE"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'INACTIVE') selected="selected"@endif>{{ __('INACTIVE') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="excerpt">{{ __('Excerpt') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'excerpt',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'excerpt')
                                        ])
                                        <textarea class="form-control" name="excerpt" data-toggle="tooltip" data-placement="top" title="A snippet of the content on your page.">@if (isset($dataTypeContent->excerpt)){{ $dataTypeContent->excerpt }}@endif</textarea>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="category_id">{{ __('Page Category') }}</label>
                                        <select class="form-control" name="category_id">
                                            @foreach(TCG\Voyager\Models\Category::all() as $category)
                                                <option value="{{ $category->id }}"@if(isset($dataTypeContent->category_id) && $dataTypeContent->category_id == $category->id) selected="selected"@endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    {{-- <div class="form-group">
                                        <label for="featured">{{ __('voyager::generic.featured') }}</label>
                                        <input type="checkbox" name="featured"@if(isset($dataTypeContent->featured) && $dataTypeContent->featured) checked="checked"@endif>
                                    </div> --}}
                                </div>
                            </div>

                            <!-- ### IMAGE ### -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="icon-photo"></i> {{ __('Page Image') }}
                                    </h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    @if(isset($dataTypeContent->image))
                                    @php
                                        $imageParams = ["template" => "dynamic", "params" => ["w" => 320, "h" => 180]]
                                    @endphp
                                        <img src="{{ filter_var($dataTypeContent->image, FILTER_VALIDATE_URL) ? $dataTypeContent->image : Voyager::image( $dataTypeContent->image, "", $imageParams ) }}" style="width:100%" />
                                    @endif
                                    <input type="file" name="image">
                                    <script>
                                        function updateImageMeta() {
                                            var imageMetaData = {
                                                'caption' : $('#image_caption').val(),
                                                'alt' : $('#image_alt').val(),
                                            };
                                            
                                            $('#image_meta').val(JSON.stringify(imageMetaData));
                                        }
                                    </script>
                                    <div class="form-group">
                                        <label for="image_alt">{{ __('Alternative Text') }}</label>
                                        <input class="form-control" data-toggle="tooltip" data-placement="top" title="Alternative text to show in case image fails to load." onkeyup="updateImageMeta();" onkeydown="updateImageMeta();" onchange="updateImageMeta();" id="image_alt" value="@if(isset($dataTypeContent->image_meta) && json_decode($dataTypeContent->image_meta)){{ json_decode($dataTypeContent->image_meta)->alt }}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label for="image_caption">{{ __('Caption') }}</label>
                                        <textarea class="form-control" data-toggle="tooltip" data-placement="top" title="Caption of the image" onkeyup="updateImageMeta();" onkeydown="updateImageMeta();" onchange="updateImageMeta();" id="image_caption">@if(isset($dataTypeContent->image_meta) && json_decode($dataTypeContent->image_meta)){{ json_decode($dataTypeContent->image_meta)->caption }}@endif</textarea>
                                    </div>
                                    <input id="image_meta" type="hidden" name="image_meta">
                                </div>
                            </div>
                        </div>
                        <div id="seo" class="tab-pane custom-pane fade">
                            <!-- ### SEO CONTENT ### -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="icon-internet"></i> {{ __('voyager::post.seo_content') }}
                                        <span class="panel-desc"> {{ __('Make your page stand out on Google') }}</span>
                                    </h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="meta_description">{{ __('voyager::post.meta_description') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'meta_description',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'meta_description')
                                        ])
                                        <textarea class="form-control" id="meta_description" name="meta_description" data-toggle="tooltip" data-placement="top" title="Make search engines find your content easily with the meta description.">@if(isset($dataTypeContent->meta_description)){{ $dataTypeContent->meta_description }}@endif</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="focus_keywords">{{ __('Focus Keywords') }}</label>
                                        <textarea class="form-control" id="focus_keywords" name="focus_keywords">@if(isset($dataTypeContent->focus_keywords)){{ $dataTypeContent->focus_keywords }}@endif</textarea>
                                        <input type="hidden" id="seo_title" value="">
                                    </div>
                                </div>
                            </div>
                            <!-- ### SEO ANALYSIS ### -->
                            @include('admin::voyager.posts.seo.panel')
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary pull-right">
                @if(isset($dataTypeContent->id)){{ __('Update Page') }}@else <i class="icon wb-plus-circle"></i> {{ __('Create New Page') }} @endif
            </button>
        </form>

        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>
    </div>
    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>
                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('#slug').slugify();

        @if ($isModelTranslatable)
            $('.side-body').multilingual({"editing": true});
        @endif

        $('.side-body input[data-slug-origin]').each(function(i, el) {
               $(el).slugify();
           });
            $('.form-group').on('click', '.remove-multi-image', function (e) {
               e.preventDefault();
               $image = $(this).siblings('img');
                params = {
                   slug:   '{{ $dataType->slug }}',
                   image:  $image.data('image'),
                   id:     $image.data('id'),
                   field:  $image.parent().data('field-name'),
                   _token: '{{ csrf_token() }}'
               }
                $('.confirm_delete_name').text($image.data('image'));
               $('#confirm_delete_modal').modal('show');
           });
            $('#confirm_delete').on('click', function(){
               $.post('{{ route('voyager.media.remove') }}', params, function (response) {
                   if ( response
                       && response.data
                       && response.data.status
                       && response.data.status == 200 ) {
                        toastr.success(response.data.message);
                       $image.parent().fadeOut(300, function() { $(this).remove(); })
                   } else {
                       toastr.error("Error removing image.");
                   }
               });
                $('#confirm_delete_modal').modal('hide');
           });
           $('[data-toggle="tooltip"]').tooltip();

           @if(true)
                //Populate SEO fields
                populateFields();
                $(".form-edit-add").on("load change keyup cut paste", function() {
                    populateFormStats();
                });
            @endif
        });
    </script>
@stop
