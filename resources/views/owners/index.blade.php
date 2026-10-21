@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">@if(request()->is('owners/approved'))
                                            {{ trans('lang.approved_owners') }}
                                        @elseif(request()->is('owners/pending'))
                                            {{ trans('lang.pending_owners') }}
                                        @else
                                            {{ trans('lang.all_owners') }}
                                        @endif</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.owner_table_text') }}</p>
                </div>
                <div class="ec-page-head-actions">
                    {{-- data-toggle, not data-bs-toggle: the layout ships Bootstrap 4. --}}
                    <div class="dropdown d-inline-block mr-2">
                                <button class="btn ec-btn-ghost dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-download"></i> {{ trans('lang.export_as') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'excel', 'model' => 'Owner']) }}">{{ trans('lang.export_excel') }}</a>
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'pdf', 'model' => 'Owner']) }}">{{ trans('lang.export_pdf') }}</a>
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'csv', 'model' => 'Owner']) }}">{{ trans('lang.export_csv') }}</a>
                                </div>
                            </div>
                    <a class="btn ec-head-btn" href="{!! route('owners.create') !!}"><i class="mdi mdi-plus"></i> {{ trans('lang.owners_create') }}</a>
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
                    <a class="btn ec-btn-ghost" href="{{ route('owners.index') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('owners.index') }}" id="filterForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="prenom">
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
                                            <option value="active" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'active' ? 'selected ' : '' }}>{{ trans('lang.active') }}</option>
                                            <option value="inactive" {{ isset($_GET['status_selector']) && $_GET['status_selector'] == 'inactive' ? 'selected' : '' }}>{{ trans('lang.in_active') }}</option>
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
                                <h5 class="mb-0">@if(request()->is('owners/approved'))
                                            {{ trans('lang.approved_owners') }}
                                        @elseif(request()->is('owners/pending'))
                                            {{ trans('lang.pending_owners') }}
                                        @else
                                            {{ trans('lang.all_owners') }}
                                        @endif {{ trans('lang.list') }}</h5>
                                
                            </div>
                            <div class="card-body">
                                @can('owners.delete')
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $owners->firstItem() }} {{trans('lang.to_small')}} {{ $owners->lastItem() }} {{trans('lang.of')}} {{ $owners->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('owners.delete')
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                                @endcan
                                                <th>{{ trans('lang.owner_name') }}</th>
                                                <th>{{ trans('lang.email') }}</th>
                                                <th>{{ trans('lang.active_plan') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.dispatcher_access') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list12">
                                            @if (count($owners) > 0)
                                                @foreach ($owners as $owner)
                                                    <tr>
                                                        @can('owners.delete')
                                                        <td class="ec-check-col"><input type="checkbox" id="is_open_{{ $owner->id }}" class="is_open ec-check" dataid="{{ $owner->id }}"></td>
                                                        @endcan
                                                        <td> 
                                                            @if (file_exists(public_path('assets/images/driver' . '/' . $owner->photo_path)) && !empty($owner->photo_path))
                                                                <img class="rounded" style="width:50px" src="{{ asset('assets/images/driver') . '/' . $owner->photo_path }}" alt="image">
                                                            @else
                                                                <img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                                            @endif
                                                            <a href="{{ route('owners.show', ['id' => $owner->id]) }}"> 
                                                                {{ $owner->prenom }} {{ $owner->nom }}
                                                            </a>
                                                            @if($owner->is_verified)
                                                                <i class="mdi mdi-verified verified-icon" title="{{ trans('lang.verified') }}"></i>
                                                            @endif
                                                        </td>
                                                        <td>{{ $owner->email }}</td>
                                                        <td>
                                                            @if (!empty($owner->subscription_plan))
                                                                <span>{{ trans('lang.plan_name') }} : {{ $owner->subscription_plan['name'] }}</span><br>
                                                                <span>{{ trans('lang.expiry_date') }} : {{ $owner->subscriptionExpiryDate == null ? trans('lang.unlimited') : date('d F Y', strtotime($owner->subscriptionExpiryDate)) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($owner->statut == 'yes')
                                                                <label class="switch"><input type="checkbox" checked id="{{ $owner->id }}" name="isActive"><span class="slider round"></span></label>
                                                            @else
                                                                <label class="switch"><input type="checkbox" id="{{ $owner->id }}" name="isActive"><span class="slider round"></span></label><span>
                                                            @endif
                                                        </td>
                                                        <?php $count = 0; ?>
                                                        @if ($owner->subscriptionPlanId && !empty($owner->subscription_plan))
                                                            <td>
                                                                @if ($owner->subscription_plan['dispatcher_access'] == "yes")
                                                                    <span class="badge badge-success">{{ trans('lang.yes') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ trans('lang.no') }}</span>
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td></td>
                                                        @endif
                                                        <td class="action-btn ec-actions">
                                                            <a class="ec-act" href="{{ route('walletstransaction.driver', ['id' => $owner->id]) }}"><i class="mdi mdi-wallet" data-toggle="tooltip" title="{{ trans('lang.wallet_transaction') }}"></i></a>
                                                            <a class="ec-act" href="{{ route('driver.documentView', ['id' => $owner->id]) }}"><i class="mdi mdi-file" data-toggle="tooltip" title="{{ trans('lang.document_details') }}"></i></a>
                                                            <a class="ec-act" href="{{ route('owners.show', ['id' => $owner->id]) }}"><i class="mdi mdi-eye" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"></i></a>
                                                            <a class="ec-act" href="{{ route('owners.edit', ['id' => $owner->id]) }}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                                            @can('owners.delete')
                                                                <a class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('owners.delete', ['id' => $owner->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
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
                                            {{ $owners->links('pagination.pagination') }}
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
                ecConfirm('{{ trans('lang.selected_delete_alert') }}').then(function (ok) {
                    if (!ok) return;
                    var arrayUsers = [];
                    $('#example24 .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        arrayUsers.push(dataId);
                    });
                    arrayUsers = JSON.stringify(arrayUsers);
                    var url = "{{ url('owners/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert('{{ trans('lang.select_delete_alert') }}');
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
                url: 'owner/switch',
                method: "POST",
                data: {
                    'ischeck': ischeck,
                    'id': id
                },
                success: function(data) {
                },
            });
        });
        /*toggal publish action code end*/
    </script>
@endsection
