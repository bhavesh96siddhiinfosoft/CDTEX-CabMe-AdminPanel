@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.complaints') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.complaints_table_text') }}</p>
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
                                <h5 class="mb-0">{{ trans('lang.complaints') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('complaints.delete')
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $complaints->firstItem() }} {{trans('lang.to_small')}} {{ $complaints->lastItem() }} {{trans('lang.of')}} {{ $complaints->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('complaints.delete')
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                                @endcan 
                                                <th>{{ trans('lang.order_id') }}</th>
                                                <th>{{ trans('lang.order_type') }}</th>
                                                <th>{{ trans('lang.driver_name') }}</th>
                                                <th>{{ trans('lang.userName') }}</th>
                                                <th>{{ trans('lang.title') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.created') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @if (count($complaints) > 0)
                                                @foreach ($complaints as $complaint)
                                                    <tr>
                                                        @can('complaints.delete')
                                                        <td class="ec-check-col"><input type="checkbox" id="is_open_{{ $complaint->id }}" class="is_open ec-check" dataid="{{ $complaint->id }}">
                                                        </td>
                                                        @endcan 
                                                        <td>
                                                            @if($complaint->booking_type == "ride")
                                                                <a href="{{ route('ride.show', ['id' => $complaint->booking_id]) }}">{{ $complaint->booking_id }}</a>
                                                            @elseif($complaint->booking_type == "parcel")
                                                                <a href="{{ route('parcel.show', ['id' => $complaint->booking_id]) }}">{{ $complaint->booking_id }}</a>
                                                            @elseif($complaint->booking_type == "rental")
                                                                <a href="{{ route('rental-orders.show', ['id' => $complaint->booking_id]) }}">{{ $complaint->booking_id }}</a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ ucfirst($complaint->booking_type) }}
                                                        </td>
                                                        <td>
                                                            @if($complaint->driver)
                                                                <a href="{{ route('driver.show', ['id' => $complaint->driver->id]) }}">
                                                                    {{ $complaint->driver->prenom }} {{ $complaint->driver->nom }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($complaint->user)
                                                                <a href="{{ route('users.show', ['id' => $complaint->user->id]) }}">
                                                                    {{ $complaint->user->prenom }} {{ $complaint->user->nom }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $complaint->title }}</td>
                                                        <td>
                                                            @if ($complaint->status == 'completed')
                                                                <span class="badge badge-success">{{ $complaint->status }}</span>
                                                            @elseif($complaint->status == 'processing')
                                                                <span class="badge badge-warning"> {{ $complaint->status }}</span>
                                                            @elseif($complaint->status == 'initiated')
                                                                <span class="badge badge-primary"> {{ $complaint->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                           <span class="date">{{ date('d F Y', strtotime($complaint->created_at)) }}</span>
                                                            <span class="time">{{ date('h:i A', strtotime($complaint->created_at)) }}</span>
                                                        </td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="javascript:void(0)" id="{{ $complaint->id }}" class="complaint-show ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            @can('complaints.delete')
                                                            <a class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('complaints.delete', ['id' => $complaint->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
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
                                            {{ $complaints->links('pagination.pagination') }}
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
    <div class="modal fade" id="showComplaintModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.complaint_detail') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('complaints.update') }}" method="post" class="">
                        @csrf
                        <div class="form-row">
                            <div class="form-group row">
                                <input type="text" name="complaint_id" id="complaint_id" hidden>
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.title') }}</label>
                                    <div class="col-12 title">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.message') }}</label>
                                    <div class="col-12 message">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.status') }}</label>
                                    <div class="col-12">
                                        @php
                                            $status = ['initiated' => 'Initiated', 'processing' => 'Processing', 'completed' => 'Completed'];
                                        @endphp
                                        <select name="complaint_status" class="form-control" class="status" id="complaint_status">
                                            @foreach ($status as $key => $value)
                                                <option value="{{ $key }}"> {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn ec-btn-brand edit-form-btn" id="add-wallet-btn">{{ trans('lang.save') }}
                            </button>
                            <button type="button" class="btn ec-btn-ghost" data-dismiss="modal" aria-label="Close">
                                {{ trans('lang.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
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
                    var url = "{{ url('complaints/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert('{{ trans('lang.select_delete_alert') }}');
            }
        });
        $('.complaint-show').on('click', function() {
            var id = this.id;
            var url = "{{ url('complaints/show', 'id') }}";
            url = url.replace('id', id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('.title').text(data.title);
                    $('.message').text(data.description);
                    $('#complaint_status [value="' + data.status + '"]').attr('selected', 'true');
                    $('#complaint_id').val(id);
                    $('#showComplaintModal').modal('show');
                },
            })
        })
        $(document).ready(function() {
            if ($('#selected_search').val() == "status") {
                jQuery('#search').val('');
            } else {
                jQuery('#status').val('');
            }
        })
        $(document.body).on('change', '#selected_search', function() {
            if (jQuery(this).val() == 'status') {
                jQuery('#status').show();
                jQuery('#status').val('initiated');
                jQuery('#search').val('');
                jQuery('#search').hide();
            } else {
                jQuery('#status').hide();
                jQuery('#status').val('');
                jQuery('#search').show();
            }
        });
    </script>
@endsection
