@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.subscription_plans') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.see_overview_of_package_earning') }}</p>
                </div>
                <a class="btn ec-head-btn" href="{!! route('subscription-plans.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.create_subscription_plan') }}</a>
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
                        <input type="hidden" name="selected_search" value="name">
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
            @if (count($overviewPlans) > 0)
                <div class="overview-sec">
                    <div class="row">
                        <div class="col-12">
                            <div class="card ec-card">
                                <div class="card-header d-flex justify-content-between align-items-center border-0">
                                    <h5 class="mb-0">{{ trans('lang.subscription_plans') }} {{ trans('lang.list') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row subscription-list">
                                        @foreach ($overviewPlans as $data)
                                            <div class="col-md-4">
                                                <div class="card card-box-with-icon">
                                                    <div class="card-body">
                                                        <span class="box-icon"><img src="{{ asset('assets/images/subscription') . '/' . $data->image }}"></span>
                                                        <div class="card-box-with-content mt-3">
                                                            <h4 class="text-dark-2 mb-1 h4 ">
                                                                @if (!empty($data->total_earning))
                                                                    @php $totalEarning=$data->total_earning;@endphp
                                                                @else
                                                                    @php $totalEarning=0;@endphp
                                                                @endif
                                                                @if ($currency->symbol_at_right == 'true')
                                                                    {{ number_format($totalEarning, $currency->decimal_digit) . '' . $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole . '' . number_format($totalEarning, $currency->decimal_digit) }}
                                                                @endif
                                                            </h4>
                                                            <p class="mb-0 text-dark-2">{{ $data->name }} ({{ ucfirst($data->plan_for) }})</p>
                                                        </div>
                                                        <span class="background-img"><img src="{{ asset('assets/images/subscription') . '/' . $data->image }}"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card ec-card ec-list-card">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <h5 class="mb-0">{{ trans('lang.subscription_plans') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('subscription-plans.delete')
                                <div id="ec-bulk-bar" class="ec-bulk-bar mb-3" style="display:none">
                                    <span class="ec-bulk-count"><strong id="ec-sel-count">0</strong> {{ trans('lang.bulk_selected') }}</span>
                                    <div class="ec-bulk-actions">
                                        <a class="btn ec-btn-ghost btn-sm ec-act-danger do_not_delete" id="deleteAll"  href="javascript:void(0)"><i class="mdi mdi-delete-outline"></i> {{ trans('lang.delete') }}</a>
                                        <button type="button" class="btn ec-btn-ghost btn-sm" id="ec-bulk-clear">{{ trans('lang.bulk_clear') }}</button>
                                    </div>
                                </div>
                                @endcan
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $subscriptionPlans->firstItem() }} {{trans('lang.to_small')}} {{ $subscriptionPlans->lastItem() }} {{trans('lang.of')}} {{ $subscriptionPlans->total() }} {{trans('lang.entries')}}</div>
                                </div>

                                <div class="table-responsive m-t-10">
                                    <table id="subscriptionPlansTable" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('subscription-plans.delete')
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"><label class="control-label" for="is_active">
                                                        </label>
                                                </th>
                                                @endcan
                                                <th>{{ trans('lang.plan_name') }}</th>
                                                <th>{{ trans('lang.plan_for') }}</th>
                                                <th>{{ trans('lang.plan_price') }}</th>
                                                <th>{{ trans('lang.duration') }}</th>
                                                <th>{{ trans('lang.current_subscriber') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @if (count($subscriptionPlans) > 0)
                                                @foreach ($subscriptionPlans as $value)
                                                    <tr>
                                                        @can('subscription-plans.delete')
                                                        <td class="ec-check-col">
                                                            @if (intval($value->id) != 1)
                                                                <input type="checkbox" id="is_open_{{ $value->id }}" class="is_open ec-check" dataid="{{ $value->id }}">
                                                            @endif
                                                        </td>
                                                        @endcan
                                                        @if (file_exists(public_path('assets/images/subscription' . '/' . $value->image)) && !empty($value->image))
                                                            <td><img class="rounded" width="50px" src="{{ asset('assets/images/subscription') . '/' . $value->image }}" alt="image">
                                                            <a href="{{ route('subscription-plans.edit', ['id' => $value->id]) }}">{{ $value->name }}</a>
                                                            </td>
                                                        @else
                                                            <td><img class="rounded" width="50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                                            <a href="{{ route('subscription-plans.edit', ['id' => $value->id]) }}">{{ $value->name }}</a>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            @if($value->id == 1)
                                                                {{ trans('lang.driver_or_owner') }}
                                                            @else
                                                                {{ ucfirst($value->plan_for) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (intVal($value->price != 0))
                                                                @if ($currency->symbol_at_right == 'true')
                                                                    {{ number_format($value->price, $currency->decimal_digit) . '' . $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole . '' . number_format($value->price, $currency->decimal_digit) }}
                                                                @endif
                                                            @else
                                                                {{ trans('lang.free') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value->expiryDay == '-1')
                                                                {{ trans('lang.unlimited') }}
                                                            @else
                                                                {{ $value->expiryDay }} {{ trans('lang.days') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($value->subscribers_count > 0)
                                                                <a href="{{ route('current-subscriber.list', ['id' => $value->id]) }}">{{ $value->subscribers_count }}</a>
                                                            @else
                                                               {{ $value->subscribers_count }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (intval($value->id) != 1)
                                                                @if ($value->isEnable == 'true')
                                                                    <label class="switch"><input type="checkbox" id="{{ $value->id }}" name="isActive" checked><span class="slider round"></span></label>
                                                                @else
                                                                    <label class="switch"><input type="checkbox" id="{{ $value->id }}" name="isActive"><span class="slider round"></span></label>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td class="action-btn ec-actions">
                                                            <a class="ec-act" href="{{ route('subscription-plans.edit', ['id' => $value->id]) }}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                                            @can('subscription-plans.delete')
                                                            @if (intval($value->id) != 1)
                                                                <a href="{{ route('subscription-plans.delete', ['id' => $value->id]) }}" class="delete-btn ec-act ec-act-danger" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                            @endif
                                                            @endcan
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
                                            {{ $subscriptionPlans->links('pagination.pagination') }}
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
    <script>
        $("#is_active").click(function() {
            $("#subscriptionPlansTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function (e) {
            e.preventDefault();
            if ($('#subscriptionPlansTable .is_open:checked').length) {
                ecConfirm('{{ trans('lang.selected_delete_alert') }}').then(function (ok) {
                    if (!ok) return;
                    var arrayUsers = [];
                    $('#subscriptionPlansTable .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        arrayUsers.push(dataId);
                    });
                    arrayUsers = JSON.stringify(arrayUsers);
                    var url = "{{ url('subscription-plans/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });
        /* toggal publish action code start*/
        $(document).on("click", "input[name='isActive']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'subscription-plans-switch',
                method: "POST",
                data: {
                    'ischeck': ischeck,
                    'id': id
                },
                success: function(response) {
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                    location.reload();
                }
            });
        });
    </script>
@endsection
