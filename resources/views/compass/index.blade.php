@extends('cruid::master')

@section('css')

    @include('cruid::compass.includes.styles')

@stop

@section('page_header')
    <h1 class="page-title">
        <i class="cruid-compass"></i>
        <p> {{ __('cruid::generic.compass') }}</p>
        <span class="page-description">{{ __('cruid::compass.welcome') }}</span>
    </h1>
@stop

@section('content')

    <div id="gradient_bg"></div>

    <div class="container-fluid">
        @include('cruid::alerts')
    </div>

    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
          <li @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'resources')){!! 'class="active"' !!}@endif><a data-toggle="tab" href="#resources"><i class="cruid-book"></i> {{ __('cruid::compass.resources.title') }}</a></li>
          <li @if($active_tab == 'commands'){!! 'class="active"' !!}@endif><a data-toggle="tab" href="#commands"><i class="cruid-terminal"></i> {{ __('cruid::compass.commands.title') }}</a></li>
          <li @if($active_tab == 'logs'){!! 'class="active"' !!}@endif><a data-toggle="tab" href="#logs"><i class="cruid-logbook"></i> {{ __('cruid::compass.logs.title') }}</a></li>
        </ul>

        <div class="tab-content">
            <div id="resources" class="tab-pane fade in @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'resources')){!! 'active' !!}@endif">
                <h3><i class="cruid-book"></i> {{ __('cruid::compass.resources.title') }} <small>{{ __('cruid::compass.resources.text') }}</small></h3>

                <div class="collapsible">
                    <div class="collapse-head" data-toggle="collapse" data-target="#links" aria-expanded="true" aria-controls="links">
                        <h4>{{ __('cruid::compass.links.title') }}</h4>
                        <i class="cruid-angle-down"></i>
                        <i class="cruid-angle-up"></i>
                    </div>
                    <div class="collapse-content collapse in" id="links">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="https://cruid-docs.devdojo.com/" target="_blank" class="cruid-link" style="background-image:url('{{ cruid_asset('images/compass/documentation.jpg') }}')">
                                    <span class="resource_label"><i class="cruid-documentation"></i> <span class="copy">{{ __('cruid::compass.links.documentation') }}</span></span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="https://cruid.devdojo.com/" target="_blank" class="cruid-link" style="background-image:url('{{ cruid_asset('images/compass/cruid-home.jpg') }}')">
                                    <span class="resource_label"><i class="cruid-browser"></i> <span class="copy">{{ __('cruid::compass.links.cruid_homepage') }}</span></span>
                                </a>
                            </div>
                        </div>
                    </div>
              </div>

              <div class="collapsible">

                <div class="collapse-head" data-toggle="collapse" data-target="#fonts" aria-expanded="true" aria-controls="fonts">
                    <h4>{{ __('cruid::compass.fonts.title') }}</h4>
                    <i class="cruid-angle-down"></i>
                    <i class="cruid-angle-up"></i>
                </div>

                <div class="collapse-content collapse in" id="fonts">

                    @include('cruid::compass.includes.fonts')

                </div>

              </div>
            </div>

          <div id="commands" class="tab-pane fade in @if($active_tab == 'commands'){!! 'active' !!}@endif">
            <h3><i class="cruid-terminal"></i> {{ __('cruid::compass.commands.title') }} <small>{{ __('cruid::compass.commands.text') }}</small></h3>
            <div id="command_lists">
                @include('cruid::compass.includes.commands')
            </div>

          </div>
          <div id="logs" class="tab-pane fade in @if($active_tab == 'logs'){!! 'active' !!}@endif">
            <div class="row">

                @include('cruid::compass.includes.logs')

            </div>
          </div>
        </div>

    </div>

@stop
@section('javascript')
    <script>
        $('document').ready(function(){
            $('.collapse-head').click(function(){
                var collapseContainer = $(this).parent();
                if(collapseContainer.find('.collapse-content').hasClass('in')){
                    collapseContainer.find('.cruid-angle-up').fadeOut('fast');
                    collapseContainer.find('.cruid-angle-down').fadeIn('slow');
                } else {
                    collapseContainer.find('.cruid-angle-down').fadeOut('fast');
                    collapseContainer.find('.cruid-angle-up').fadeIn('slow');
                }
            });
        });
    </script>
    <!-- JS for commands -->
    <script>

        $(document).ready(function(){
            $('.command').click(function(){
                $(this).find('.cmd_form').slideDown();
                $(this).addClass('more_args');
                $(this).find('input[type="text"]').focus();
            });

            $('.close-output').click(function(){
                $('#commands pre').slideUp();
            });
        });

    </script>

    <!-- JS for logs -->
    <script>
      $(document).ready(function () {
        $('.table-container tr').on('click', function () {
          $('#' + $(this).data('display')).toggle();
        });
        $('#table-log').DataTable({
          "order": [1, 'desc'],
          "stateSave": true,
          "language": {!! json_encode(__('cruid::datatable')) !!},
          "stateSaveCallback": function (settings, data) {
            window.localStorage.setItem("datatable", JSON.stringify(data));
          },
          "stateLoadCallback": function (settings) {
            var data = JSON.parse(window.localStorage.getItem("datatable"));
            if (data) data.start = 0;
            return data;
          }
        });

        $('#delete-log, #delete-all-log').click(function () {
          return confirm('{{ __('cruid::generic.are_you_sure') }}');
        });
      });
    </script>
@stop
