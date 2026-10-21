@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.on_boarding_screens') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.on_boarding_table_text') }}</p>
                </div>
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
                        <input type="hidden" name="selected_search" value="title">
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
                                <h5 class="mb-0">{{ trans('lang.on_boarding_screens') }} {{ trans('lang.list') }}</h5>
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $onboarding->firstItem() }} {{trans('lang.to_small')}} {{ $onboarding->lastItem() }} {{trans('lang.of')}} {{ $onboarding->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display  table table-hover table-striped table-bordered table table-striped ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('lang.title') }}</th>
                                                <th>{{ trans('lang.description') }}</th>
                                                <th>{{ trans('lang.onboarding_type') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @if (count($onboarding) > 0)
                                                @foreach ($onboarding as $value)
                                                    <tr>
                                                        @if (file_exists(public_path('assets/images/onboarding' . '/' . $value->image)) && !empty($value->image))
                                                            <td><img class="rounded" style="width:50px" src="{{ asset('assets/images/onboarding') . '/' . $value->image }}" alt="image">
                                                            <a href="{{ route('on-boarding.edit', ['id' => $value->id]) }}">{{ $value->title }}</a>
                                                            </td>
                                                        @else
                                                            <td><img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                                            <a href="{{ route('on-boarding.edit', ['id' => $value->id]) }}">{{ $value->title }}</a>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            {{ $value->description }}
                                                        </td>
                                                        <td>
                                                            {{ $value->type }}
                                                        </td>
                                                        <td class="action-btn ec-actions">
                                                            <a class="ec-act" href="{{ route('on-boarding.edit', ['id' => $value->id]) }}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="11" align="center">{{ trans('lang.no_result') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $onboarding->links('pagination.pagination') }}
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
    </script>
@endsection
