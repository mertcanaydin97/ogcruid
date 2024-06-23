@extends('cruid::master')

@section('page_title', __('cruid::generic.media'))

@section('content')
    <div class="page-content container-fluid">
        @include('cruid::alerts')
        <div class="row">
            <div class="col-md-12">

                <div class="admin-section-title">
                    <h3><i class="cruid-images"></i> {{ __('cruid::generic.media') }}</h3>
                </div>
                <div class="clear"></div>
                <div id="filemanager">
                    <media-manager
                        base-path="{{ config('cruid.media.path', '/') }}"
                        :show-folders="{{ config('cruid.media.show_folders', true) ? 'true' : 'false' }}"
                        :allow-upload="{{ config('cruid.media.allow_upload', true) ? 'true' : 'false' }}"
                        :allow-move="{{ config('cruid.media.allow_move', true) ? 'true' : 'false' }}"
                        :allow-delete="{{ config('cruid.media.allow_delete', true) ? 'true' : 'false' }}"
                        :allow-create-folder="{{ config('cruid.media.allow_create_folder', true) ? 'true' : 'false' }}"
                        :allow-rename="{{ config('cruid.media.allow_rename', true) ? 'true' : 'false' }}"
                        :allow-crop="{{ config('cruid.media.allow_crop', true) ? 'true' : 'false' }}"
                        :details="{{ json_encode(['thumbnails' => config('cruid.media.thumbnails', []), 'watermark' => config('cruid.media.watermark', (object)[])]) }}"
                        ></media-manager>
                </div>
            </div><!-- .row -->
        </div><!-- .col-md-12 -->
    </div><!-- .page-content container-fluid -->
@stop

@section('javascript')
<script>
new Vue({
    el: '#filemanager'
});
</script>
@endsection
