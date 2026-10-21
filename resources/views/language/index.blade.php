@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.languages') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.language_table_text') }}</p>
                </div>
                <a class="btn ec-head-btn" href="{!! route('language.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.language_create') }}</a>
            </div>

            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ url()->current() }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="language">
                        <div class="row ec-filter-row align-items-end">
                            <div class="col-lg-6 col-md-12 mb-2">
                                <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                                <div class="ec-search-field">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" class="form-control" name="search" id="search"
                                           value="{{ request('search') }}" placeholder="{{ trans('lang.search_here') }}"
                                           autocomplete="off" spellcheck="false">
                                </div>
                            </div>

                            

                            

                            
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card ec-card ec-list-card">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <h5 class="mb-0">{{ trans('lang.languages') }} {{ trans('lang.list') }}</h5>
                                
                            </div>
                            <div class="card-body">
                                <div class="ec-list-toolbar">
                                    <div class="ec-length">
                                        <span>{{ trans('lang.show') }}</span>
                                        <select form="perPageForm" name="per_page" id="per_page" class="form-control">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $language->firstItem() }} {{trans('lang.to_small')}} {{ $language->lastItem() }} {{trans('lang.of')}} {{ $language->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}
                                </div>
                                
                                <div class="table-responsive m-t-10">
                                    <div class="error_top"></div>
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('lang.languages') }}</th>
                                                <th>{{ trans('lang.code') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list12">
                                            @foreach ($language as $customer)
                                                <tr>
                                                    @if (file_exists(public_path('assets/images/flags' . '/' . $customer->flag)) && !empty($customer->flag))
                                                        <td><img class="rounded" style="width:50px" src="{{ asset('assets/images/flags') . '/' . $customer->flag }}" alt="image">{{ $customer->language }}</td>
                                                    @else
                                                        <td><img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">{{ $customer->language }}</td>
                                                    @endif
                                                    <td>{{ $customer->code }}</td>
                                                    <td>
                                                        @if ($customer->status == 'true')
                                                            <label class="switch"><input type="checkbox" checked id="{{ $customer->id }}" name="isSwitch"><span class="slider round"></span></label>
                                                        @else
                                                            <label class="switch"><input type="checkbox" id="{{ $customer->id }}" name="isSwitch"><span class="slider round"></span></label>
                                                        @endif
                                                    </td>
                                                    <td class="action-btn ec-actions"><a class="ec-act" href="{{ route('language.edit', ['id' => $customer->id]) }}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                                    @can('language.delete')
                                                    <a id="'+val.id+'" class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('language.delete', ['id' => $customer->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                    @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>                                    
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $language->links('pagination.pagination') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".shadow-sm").hide();
        })
        var status = $("input[name='isSwitch']").val();
        $(document).on("click", "input[name='isSwitch']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            var url = "{{ route('language.switch') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                method: "POST",
                data: {
                    'ischeck': ischeck,
                    'id': id
                },
                success: function(data) {
                    console.log(data.error);
                    if (data.error) {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").after("<div class='alert alert-danger'><ul><li>" + data.error + "</li></ul></div>");
                    }
                    window.location.reload();
                },
                error: function(response) {
                },
            });
        });
    </script>
@endsection
