@extends('admin::voyager.master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form" action="@if(isset($dataTypeContent->id)){{ route('voyager.posts.update', $dataTypeContent->id) }}@else{{ route('voyager.posts.store') }}@endif" method="POST" enctype="multipart/form-data">
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
                                <i class="icon-pencil2"></i> {{ __('voyager::post.title') }}
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
                                <i class="icon-newspaper"></i> {{ __('voyager::post.content') }}
                            </h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-resize-full" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                            </div>
                        </div>

                        <div class="panel-body">
                            @include('voyager::multilingual.input-hidden', [
                                '_field_name'  => 'body',
                                '_field_trans' => get_field_translations($dataTypeContent, 'body')
                            ])
                            @php
                                $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};
                                $row = $dataTypeRows->where('field', 'body')->first();
                            @endphp
                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                        </div>
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
                                $exclude = ['title', 'body', 'excerpt', 'slug', 'status', 'category_id', 'author_id', 'featured', 'image', 'meta_description', 'meta_keywords', 'seo_title'];
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
                                        <i class="icon-link-2"></i> {{ __('voyager::post.details') }}
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
                                        <input type="text" class="form-control" id="slug" name="slug" data-toggle="tooltip" data-placement="top" title="The final URL will include this slug for better SEO."
                                            placeholder="slug"
                                            {!! isFieldSlugAutoGenerator($dataType, $dataTypeContent, "slug") !!}
                                            value="@if(isset($dataTypeContent->slug)){{ $dataTypeContent->slug }}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label for="status">{{ __('voyager::post.status') }}</label>
                                        <select class="form-control" name="status">
                                            <option value="PUBLISHED"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'PUBLISHED') selected="selected"@endif>{{ __('PUBLISHED') }}</option>
                                            <option value="DRAFT"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'DRAFT') selected="selected"@endif>{{ __('DRAFT') }}</option>
                                            <option value="PENDING"@if(isset($dataTypeContent->status) && $dataTypeContent->status == 'PENDING') selected="selected"@endif>{{ __('PENDING') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">{{ __('voyager::post.category') }}</label>
                                        <select class="form-control" name="category_id">
                                            @foreach(TCG\Voyager\Models\Category::all() as $category)
                                                <option value="{{ $category->id }}"@if(isset($dataTypeContent->category_id) && $dataTypeContent->category_id == $category->id) selected="selected"@endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="excerpt">{{ __('Excerpt') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'excerpt',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'excerpt')
                                        ])
                                        <textarea class="form-control" name="excerpt" data-toggle="tooltip" data-placement="top" title="A snippet of your post.">@if (isset($dataTypeContent->excerpt)){{ $dataTypeContent->excerpt }}@endif</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="featured">{{ __('voyager::generic.featured') }}</label>
                                        <input type="checkbox" name="featured"@if(isset($dataTypeContent->featured) && $dataTypeContent->featured) checked="checked"@endif>
                                    </div>
                                </div>
                            </div>

                            <!-- ### IMAGE ### -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="icon-photo"></i> {{ __('voyager::post.image') }}
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
                                </div>
                            </div>
                        </div>
                        <div id="seo" class="tab-pane custom-pane fade">
                            <!-- ### SEO CONTENT ### -->
                            <div class="panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="icon-internet"></i> {{ __('voyager::post.seo_content') }}
                                        <span class="panel-desc"> {{ __('Make your post stand out on Google') }}</span>
                                    </h3>
                                    <div class="panel-actions">
                                        <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="seo_title">{{ __('voyager::post.seo_title') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'seo_title',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'seo_title')
                                        ])
                                        <input type="text" class="form-control" data-toggle="tooltip" data-placement="top" title="A search engine - Friendly title that matches search terms of users." name="seo_title" placeholder="SEO Title" value="@if(isset($dataTypeContent->seo_title)){{ $dataTypeContent->seo_title }}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_description">{{ __('voyager::post.meta_description') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'meta_description',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'meta_description')
                                        ])
                                        <textarea class="form-control" name="meta_description">@if(isset($dataTypeContent->meta_description)){{ $dataTypeContent->meta_description }}@endif</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_keywords">{{ __('voyager::post.meta_keywords') }}</label>
                                        @include('voyager::multilingual.input-hidden', [
                                            '_field_name'  => 'meta_keywords',
                                            '_field_trans' => get_field_translations($dataTypeContent, 'meta_keywords')
                                        ])
                                        <textarea class="form-control" name="meta_keywords">@if(isset($dataTypeContent->meta_keywords)){{ $dataTypeContent->meta_keywords }}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary pull-right">
                @if(isset($dataTypeContent->id)){{ __('voyager::post.update') }}@else <i class="icon wb-plus-circle"></i> {{ __('voyager::post.new') }} @endif
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
        });
    </script>
@stop
