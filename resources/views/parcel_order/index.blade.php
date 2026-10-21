@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.parcel_orders') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.parcel_table_text') }}</p>
                </div>
                <div class="ec-page-head-actions">
                    {{-- data-toggle, not data-bs-toggle: the layout ships Bootstrap 4. --}}
                    <div class="dropdown d-inline-block mr-2">
                                <button class="btn ec-btn-ghost dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-download"></i> {{ trans('lang.export_as') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'excel', 'model' => 'ParcelOrder']) }}">{{ trans('lang.export_excel') }}</a>
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'pdf', 'model' => 'ParcelOrder']) }}">{{ trans('lang.export_pdf') }}</a>
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'csv', 'model' => 'ParcelOrder']) }}">{{ trans('lang.export_csv') }}</a>
                                </div>
                            </div>
                </div>
            </div>

            {{-- One form for every filter: search, status and date submit together
                 instead of the old three forms dropping each other's values. The
                 status select and date range keep .filteredRecords — the page script
                 submits #filterForm the moment either changes. --}}
            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ route('parcel.index') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('parcel.index') }}" id="filterForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="userName">
                        <div class="row ec-filter-row align-items-end">
                            <div class="col-lg-4 col-md-12 mb-2">
                                <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                                <div class="ec-search-field">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" class="form-control" name="search" id="search"
                                           value="{{ request('search') }}" placeholder="{{ trans('lang.search_here') }}"
                                           autocomplete="off" spellcheck="false">
                                </div>
                            </div>

                            

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <label class="ec-filter-label" for="status_selector">{{ trans('lang.status') }}</label>
                                <select class="form-control status_selector filteredRecords" name="status_selector">
                                            <option value="">{{ trans('lang.status') }}</option>
                                            <option value="confirmed" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'confirmed' ? 'selected ' : '' }}>{{ trans('lang.confirmed') }}</option>
                                            <option value="new" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'new' ? 'selected ' : '' }}>{{ trans('lang.new') }}</option>
                                            <option value="on ride" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'on ride' ? 'selected ' : '' }}>{{ trans('lang.on_ride') }}</option>
                                            <option value="completed" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'completed' ? 'selected ' : '' }}>{{ trans('lang.completed') }}</option>
                                            <option value="canceled" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'canceled' ? 'selected ' : '' }}>{{ trans('lang.canceled') }}</option>
                                            <option value="rejected" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'rejected' ? 'selected ' : '' }}>{{ trans('lang.driver_rejected') }}</option>
                                        </select>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <label class="ec-filter-label" for="daterange">{{ trans('lang.created') }}</label>
                                <input type="text" placeholder="dd-mm-yyyy" class="form-control filteredRecords" id="daterange" name="daterange" value="{{ request('daterange') }}" readonly />
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
                                <h5 class="mb-0">{{ trans('lang.parcel_orders') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('parcel.delete')
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
                                        <select form="filterForm" name="per_page" id="per_page" class="form-control">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $rides->firstItem() }} {{trans('lang.to_small')}} {{ $rides->lastItem() }} {{trans('lang.of')}} {{ $rides->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                <h4 class="card-title"></h4>
                                
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('parcel.delete')
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                                @endcan
                                                <th>{{ trans('lang.order_id') }}</th>
                                                <th>{{ trans('lang.order_number') }}</th>
                                                <th>{{ trans('lang.userName') }}</th>
                                                <th>{{ trans('lang.driver_name') }}</th>
                                                <th>{{ trans('lang.cost_amount') }}</th>
                                                <th>{{ trans('lang.booked_by') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.created') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list12">
                                            @if (count($rides) > 0)
                                                @foreach ($rides as $ride)
                                                    <tr>
                                                        @can('parcel.delete')
                                                        <td class="ec-check-col"><input type="checkbox" id="is_open_{{ $ride->id }}" class="is_open ec-check" dataid="{{ $ride->id }}"></td>
                                                        @endcan
                                                        <td><a href="{{ route('parcel.show', ['id' => $ride->id]) }}">{{ $ride->id }}</a></td>
                                                        <td><a href="{{ route('parcel.show', ['id' => $ride->id]) }}">{{ $ride->booking_number }}</a></td>  
                                                        <td>
                                                            @if($ride->user_id)
                                                                <a href="{{ route('users.show', ['id' => $ride->user_id]) }}">{{ $ride->userPrenom }} {{ $ride->userNom }}</a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(isset($ride->driver_id))
                                                                <a href="{{ route('driver.show', ['id' => $ride->driver_id]) }}">{{ $ride->driverPrenom }} {{ $ride->driverNom }}</a>
                                                            @endif
                                                        </td>
                                                        <?php 
                                                        $total_price = floatval($ride->amount) - floatval($ride->discount);
                                                        $tax = json_decode($ride->tax, true);
                                                        $totalTaxAmount = 0;
                                                        if (!empty($tax)) {
                                                            for ($i = 0; $i < sizeof($tax); $i++) {
                                                                $data = $tax[$i];
                                                                if ($data['type'] == 'Percentage') {
                                                                    $taxValue = (floatval($data['value']) * $total_price) / 100;
                                                                } else {
                                                                    $taxValue = floatval($data['value']);
                                                                }
                                                                $totalTaxAmount += floatval($taxValue);
                                                            }
                                                            $total_price = floatval($total_price) + $totalTaxAmount;
                                                            if ($ride->tip != null && $ride->tip > 0) {
                                                                $total_price = floatval($total_price) + $ride->tip;
                                                            }
                                                        }
                                                        ?>
                                                        <td>
                                                            @if ($currency->symbol_at_right == 'true')
                                                                {{ number_format(floatval($total_price), $currency->decimal_digit) . '' . $currency->symbole }}
                                                            @else
                                                                {{ $currency->symbole . '' . number_format(floatval($total_price), $currency->decimal_digit) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($ride->ride_type == 'dispatcher')
                                                                {{ trans('lang.dispatcher') }}
                                                            @else
                                                                {{ trans('lang.customer') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($ride->status == 'completed')
                                                                <span class="badge badge-success">{{ $ride->status }}<span>
                                                            @elseif($ride->status == 'confirmed')
                                                                <span class="badge badge-success">{{ $ride->status }}<span>
                                                            @elseif($ride->status == 'new')
                                                                <span class="badge badge-primary">{{ $ride->status }}<span>
                                                            @elseif($ride->status == 'rejected')
                                                                <span class="badge badge-danger">{{ $ride->status }}<span>
                                                            @elseif($ride->status == 'driver_rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.driver_rejected') }}<span>
                                                            @else
                                                                <span class="badge badge-warning">{{ $ride->status }}<span>
                                                            @endif
                                                        </td>
                                                        <td class="dt-time"><span class="date">{{ date('d F Y', strtotime($ride->created_at)) }}</span>
                                                            <span class="time">{{ date('h:i A', strtotime($ride->created_at)) }}</span>
                                                        </td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('parcel.show', ['id' => $ride->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            @can('parcel.delete')
                                                            <a id="'+val.id+'" class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('parcel.delete', ['rideid' => $ride->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
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
                                            {{ $rides->links('pagination.pagination') }}
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

        $("#is_active").click(function() {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });
        $('.status_selector').select2({
            placeholder: '{{ trans('lang.status') }}',
            minimumResultsForSearch: Infinity,
            allowClear: true
        });
        $('select').on("select2:unselecting", function(e) {
            var self = $(this);
            setTimeout(function() {
                self.select2('close');
            }, 0);
        });
        function setDate() {
            let initialDateRange = $('#daterange').val(); // Get the initial value from input
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD-MM-YYYY',
                    cancelLabel: "{{trans('lang.clear')}}"
                }
            });
            if (initialDateRange) {
                let dates = initialDateRange.split(' - ');
                $('#daterange').data('daterangepicker').setStartDate(dates[0]);
                $('#daterange').data('daterangepicker').setEndDate(dates[1]);
                $('#daterange').val(initialDateRange);
                $('#daterange').attr('readonly', true);
            }
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                $('.filteredRecords').trigger('change');
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('.filteredRecords').trigger('change');
            });
        }
        setDate();
        $('.filteredRecords').change(async function() {
            $('#filterForm').submit();
        })
        $("#deleteAll").click(function (e) {
            e.preventDefault();
            if ($('#example24 .is_open:checked').length) {
                ecConfirm("{{trans('lang.selected_delete_alert')}}").then(function (ok) {
                    if (!ok) return;
                    var arrayUsers = [];
                    $('#example24 .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        arrayUsers.push(dataId);
                    });
                    arrayUsers = JSON.stringify(arrayUsers);
                    var url = "{{ url('parcel/delete', 'rideid') }}";
                    url = url.replace('rideid', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });

        $(document).ready(function() {
            if ($('#selected_search').val() == "type") {
                jQuery('#search').hide();
                jQuery('#ride_type').show();
            }
        })
        $(document.body).on('change', '#selected_search', function() {
            if ($('#selected_search').val() == "type") {
                jQuery('#search').hide();
                jQuery('#ride_type').show();
            }else{
                jQuery('#search').show();
                jQuery('#ride_type').hide();
            }
        });
        
    </script>
@endsection
