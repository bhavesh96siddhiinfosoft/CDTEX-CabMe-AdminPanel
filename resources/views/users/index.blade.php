@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        

        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.user_table') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.users_table_text') }}</p>
                </div>
                <div class="ec-page-head-actions">
                    {{-- data-toggle, not data-bs-toggle: the layout ships the theme's
                         Bootstrap 4, whose dropdown plugin only reads the BS4 attribute. --}}
                    <div class="dropdown d-inline-block">
                        <button class="btn ec-btn-ghost dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-download"></i> {{ trans('lang.export_as') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                            <a class="dropdown-item" href="{{ route('export.data', ['type' => 'excel', 'model' => 'UserApp']) }}">{{ trans('lang.export_excel') }}</a>
                            <a class="dropdown-item" href="{{ route('export.data', ['type' => 'pdf', 'model' => 'UserApp']) }}">{{ trans('lang.export_pdf') }}</a>
                            <a class="dropdown-item" href="{{ route('export.data', ['type' => 'csv', 'model' => 'UserApp']) }}">{{ trans('lang.export_csv') }}</a>
                        </div>
                    </div>
                    <a class="btn ec-head-btn" href="{!! route('users.create') !!}">
                        <i class="mdi mdi-plus"></i> {{ trans('lang.user_create') }}
                    </a>
                </div>
            </div>

            {{-- One form for every filter. The old page split them across three forms,
                 so changing the status dropped the active search and vice versa; here
                 they submit together. The status select and the date range keep the
                 .filteredRecords class — the script below submits the form the moment
                 either changes, so those two need no Search click. --}}
            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ url('users') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="prenom">
                        <div class="row ec-filter-row align-items-end">
                            <div class="col-lg-4 col-md-12 mb-2">
                                <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                                <div class="ec-search-field">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" class="form-control" name="search" id="search"
                                           value="{{ request('search') }}"
                                           placeholder="{{ trans('lang.search_here') }}"
                                           autocomplete="off" spellcheck="false">
                                </div>
                            </div>

                            

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <label class="ec-filter-label" for="status_selector">{{ trans('lang.status') }}</label>
                                <select class="form-control status_selector filteredRecords" name="status_selector" id="status_selector">
                                    <option value="">{{ trans('lang.status') }}</option>
                                    <option value="active" {{ request('status_selector') == 'active' ? 'selected' : '' }}>{{ trans('lang.active') }}</option>
                                    <option value="inactive" {{ request('status_selector') == 'inactive' ? 'selected' : '' }}>{{ trans('lang.in_active') }}</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <label class="ec-filter-label" for="daterange">{{ trans('lang.created') }}</label>
                                <input type="text" placeholder="dd-mm-yyyy" class="form-control filteredRecords"
                                       id="daterange" name="daterange" value="{{ request('daterange') }}" readonly />
                            </div>

                            

                            
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card ec-card ec-list-card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ trans('lang.user_plural') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('users.delete')
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
                                    @foreach ([10, 20, 30, 50, 100] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">{{ trans('lang.showing') }} {{ $users->firstItem() }} {{ trans('lang.to_small') }} {{ $users->lastItem() }} {{ trans('lang.of') }} {{ $users->total() }} {{ trans('lang.entries') }}</div>
                                </div>
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('users.delete')
                                                    <th class="ec-check-col">
                                                        <input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}">
                                                        <label class="control-label" for="is_active">
                                                            
                                                        </label>
                                                    </th>
                                                @endcan
                                                <th>{{ trans('lang.user_name') }}</th>
                                                <th>{{ trans('lang.email') }}</th>
                                                <th>{{ trans('lang.user_phone') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list12">
                                            @forelse ($users as $customer)
                                                <tr>
                                                    @can('users.delete')
                                                        <td class="ec-check-col">
                                                            <input type="checkbox" id="is_open_{{ $customer->id }}" class="is_open ec-check" dataid="{{ $customer->id }}">
                                                        </td>
                                                    @endcan
                                                    <td>
                                                        @if (!empty($customer->photo_path) && file_exists(public_path('assets/images/users/' . $customer->photo_path)))
                                                            <img class="rounded" style="width:50px" src="{{ asset('assets/images/users') . '/' . $customer->photo_path }}" alt="image">
                                                        @else
                                                            <img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                                        @endif
                                                        <a href="{{ route('users.show', ['id' => $customer->id]) }}">{{ $customer->prenom }} {{ $customer->nom }}</a>
                                                    </td>
                                                    <td>{{ $customer->email }}</td>
                                                    <td>{{ $customer->country_code }}{{ $customer->phone }}</td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" id="{{ $customer->id }}" name="isActive" {{ $customer->statut == 'yes' ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td class="action-btn ec-actions">
                                                        <a class="ec-act" href="{{ route('walletstransaction.user', ['id' => $customer->id]) }}" data-toggle="tooltip" title="{{ trans('lang.wallet_history') }}"><i class="mdi mdi-wallet"></i></a>
                                                        <a class="ec-act" href="{{ route('users.show', ['id' => $customer->id]) }}" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                        <a class="ec-act" href="{{ route('users.edit', ['id' => $customer->id]) }}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                                        @can('users.delete')
                                                            <a href="{{ route('users.delete', ['id' => $customer->id]) }}" class="delete-btn ec-act ec-act-danger" name="user-delete" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" align="center">{{ trans('lang.no_result') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $users->links('pagination.pagination') }}
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
        $("#is_active").click(function() {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });
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
                    var url = "{{ url('user/delete', 'id') }}";
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
            console.log(id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('switch') }}',
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
