@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    
    <div class="container-fluid">
     <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.car_models') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.car_model_table_text') }}</p>
                </div>
                <a class="btn ec-head-btn" href="{!! route('car-model.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.car_model_create') }}</a>
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
                        <input type="hidden" name="selected_search" value="type">
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
        <div class="row">
            <div class="col-12">
                <div class="card ec-card ec-list-card">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <h5 class="mb-0">{{ trans('lang.car_models') }} {{ trans('lang.list') }}</h5>
                                
                            </div>
                    <div class="card-body">
                                @can('car-model.delete')
                                <div id="ec-bulk-bar" class="ec-bulk-bar mb-3" style="display:none">
                                    <span class="ec-bulk-count"><strong id="ec-sel-count">0</strong> {{ trans('lang.bulk_selected') }}</span>
                                    <div class="ec-bulk-actions">
                                        <a class="btn ec-btn-ghost btn-sm ec-act-danger do_not_delete" id="deleteAll" href="javascript:void(0)"><i class="mdi mdi-delete-outline"></i> {{ trans('lang.delete') }}</a>
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $carModel->firstItem() }} {{trans('lang.to_small')}} {{ $carModel->lastItem() }} {{trans('lang.of')}} {{ $carModel->total() }} {{trans('lang.entries')}}</div>
                                </div>
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                             style="display: none;">{{trans('lang.processing')}}
                        </div>
                        
                        <div class="table-responsive m-t-10">
                            <table id="example24"
                                   class="display nowrap table table-hover ec-list-table"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    @can('car-model.delete')
                                    <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}">
                                        <label class="control-label" for="is_active">
                                        </label>
                                    </th>
                                    @endcan
                                    <th>{{trans('lang.model_name')}}</th>
                                    <th>{{trans('lang.brand')}}</th>
                                    <th>{{trans('lang.vehicle_type')}}</th>
                                    <th>{{trans('lang.status')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list12">
                                @if(count($carModel) > 0)
                                    @foreach($carModel as $value)
                                    <tr>
                                        @can('car-model.delete')
                                        <td class="ec-check-col">
                                            <input type="checkbox" id="is_open_{{$value->id}}" class="is_open ec-check" dataid="{{$value->id}}">
                                        </td>
                                        @endcan
                                        <td>
                                            <a href="{{route('car-model.edit', ['id' => $value->id])}}">
                                                {{ $value->name}}
                                            </a>
                                        </td>
                                        <td>
                                            @foreach ($brand as $brandVal)
                                                @if($value->brand_id==$brandVal->id)
                                                <a href="{{route('brand.edit', ['id' => $brandVal->id])}}">{{$brandVal->name}}</a>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>@foreach ($vehicleType as $brandVal)
                                                @if($value->vehicle_type_id==$brandVal->id)
                                                    {{$brandVal->libelle}}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td> @if ($value->status=="yes")
                                            <label class="switch"><input type="checkbox" id="{{$value->id}}" name="isActive"
                                                                        checked><span class="slider round"></span></label>
                                            @else
                                            <label class="switch"><input type="checkbox" id="{{$value->id}}" name="isActive"><span
                                                        class="slider round"></span></label>
                                            @endif
                                        </td>
                                        <td class="action-btn ec-actions"><a class="ec-act" href="{{route('car-model.edit', ['id' => $value->id])}}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i
                                                        class="mdi mdi-lead-pencil"></i></a>
                                        @can('car-model.delete')
                                            <a class="ec-act" href="{{ url('car-model/delete') }}?ids={{ $value->id }}" 
                                            class="delete-btn ec-act ec-act-danger" 
                                            data-toggle="tooltip" 
                                            title="{{ trans('lang.delete') }}">
                                            <i class="mdi mdi-delete-outline"></i>
                                            </a>
                                        @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="11" align="center">{{trans("lang.no_result")}}</td>
                                </tr>
                                @endif
                                </tbody>
                            </table>                          
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $carModel->links('pagination.pagination') }}
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
    $(document).ready(function () {
        $(".shadow-sm").hide();
    })
    $("#is_active").click(function () {
        $("#example24 .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function (e) {
        e.preventDefault();
        if ($('#example24 .is_open:checked').length) {
            ecConfirm("{{trans('lang.selected_delete_alert')}}").then(function (ok) {
                if (!ok) return;
              
                var arrayUsers = [];
                $('#example24 .is_open:checked').each(function () {
                    arrayUsers.push($(this).attr('dataId'));
                });
                var ids = arrayUsers.join(',');
                var url = "{{ url('car-model/delete') }}" + "?ids=" + ids;
                window.location.href = url;
            });
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    $(document).on("click", "input[name='isActive']", function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'carModel/switch',
            method: "POST",
            data: {'ischeck': ischeck, 'id': id},
            success: function (data) {
            },
        });
    });

</script>

@endsection
