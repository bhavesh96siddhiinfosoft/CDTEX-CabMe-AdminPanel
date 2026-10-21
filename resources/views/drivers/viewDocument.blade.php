@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.driver_document_details') }}</h3>
                <p class="mb-0 text-muted">{{ $driver->prenom }} {{ $driver->nom }} — {{ trans('lang.driver_document_list') }}</p>
            </div>
            <a href="{{ route('driver.show', ['id' => $driver->id]) }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.cancel') }}</a>
        </div>

        <div class="card ec-card ec-form-card">
            <div class="card-header d-flex align-items-center">
                <span class="ec-filter-badge"><i class="mdi mdi-file-document-multiple-outline"></i></span>
                <h5 class="mb-0 ml-2">{{ trans('lang.driver_document_list') }} — {{ $driver->prenom }} {{ $driver->nom }}</h5>
            </div>
            <div class="card-body">
                <div class="error_top"></div>

                <div class="doc-grid doc-body">
                    @forelse ($admin_documents as $document)
                        @php
                            $row = $document->driver_document;
                            $state = $row ? $row->document_status : null;
                            // DriverController saves 'Approved' and 'Disapprove'. Both
                            // spellings of each are accepted so older rows still read
                            // correctly — comparing against one literal is what made an
                            // approved document keep showing as Pending.
                            $isApproved = $row && in_array($state, ['Approved', 'Approve'], true);
                            $isRejected = $row && in_array($state, ['Disapprove', 'Disapproved', 'Rejected'], true);
                            $statusClass = !$row ? 'pending' : ($isApproved ? 'approved' : ($isRejected ? 'rejected' : 'uploaded'));
                            $statusLabel = !$row ? trans('lang.not_uploaded') : ($isApproved ? trans('lang.approved') : ($isRejected ? trans('lang.rejected') : trans('lang.pending')));
                            $file = $row && $row->document_path ? asset('assets/images/driver/documents') . '/' . $row->document_path : '';
                            $uploadUrl = url('/uploaddocument', ['id' => $row ? $row->driver_id : $driver->id, 'document_id' => $document->id]);
                        @endphp
                        <div class="doc-card" data-status="{{ $statusClass }}">
                            <div class="doc-card-head">
                                <span class="doc-card-icon"><i class="mdi mdi-file-document-outline"></i></span>
                                <div class="doc-card-title">
                                    <div class="doc-card-name">{{ $document->title }}</div>
                                    <div class="doc-card-sub">{{ $row && $row->comment ? $row->comment : trans('lang.document') }}</div>
                                </div>
                                <span class="doc-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>
                            <div class="doc-card-images">
                                <div class="doc-image-wrap">
                                    <div class="doc-image-label">{{ trans('lang.document') }}</div>
                                    @if ($file)
                                        <img src="{{ $file }}" data-full="{{ $file }}" alt="" onerror="ecDocImgError(this)">
                                    @else
                                        <div class="doc-no-image">{{ trans('lang.not_uploaded_yet') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="doc-card-actions">
                                <a class="btn ec-btn-ghost btn-sm" href="{{ $uploadUrl }}">
                                    <i class="mdi {{ $file ? 'mdi-pencil-outline' : 'mdi-cloud-upload-outline' }}"></i>
                                    {{ $file ? trans('lang.edit') : trans('lang.upload') }}
                                </a>
                                @if ($row)
                                    @unless ($isApproved)
                                        <a href="{{ route('drivers.documentstatus', ['id' => $row->id, 'type' => 1]) }}" class="btn btn-sm doc-btn-approve edit-form-btn" data-driverid="{{ $driver->id }}" data-doctitle="{{ $document->title }}"><i class="mdi mdi-check"></i> {{ trans('lang.approve') }}</a>
                                    @endunless
                                    @unless ($isRejected)
                                        <a href="javascript:void(0)" class="btn btn-sm doc-btn-reject" data-toggle="modal" data-target="#commentModal" data-docid="{{ $row->id }}" data-driverid="{{ $driver->id }}" data-doctitle="{{ $document->title }}"><i class="mdi mdi-close"></i> {{ trans('lang.reject') }}</a>
                                    @endunless
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="doc-empty text-muted">{{ trans('lang.no_record_found') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- full-size preview of a document image --}}
<div class="doc-lightbox" id="doc_lightbox"><img alt=""></div>

<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">{{ trans('lang.add_comment_disapprove') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="ec-form-label" for="comment">{{ trans('lang.comment') }}</label>
                <textarea class="form-control" id="comment" rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn ec-btn-brand send-msg">{{ trans('lang.save') }}</button>
                <button type="button" class="btn ec-btn-ghost" data-dismiss="modal">{{ trans('lang.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    // a stored file that has gone missing shows a text tile, not a broken image
    function ecDocImgError(img) {
        $(img).replaceWith('<div class="doc-no-image">{{ trans('lang.image_not_available') }}</div>');
    }
    $(document).on('click', '.doc-image-wrap img', function () {
        var url = $(this).data('full');
        if (!url) return;
        $('#doc_lightbox img').attr('src', url);
        $('#doc_lightbox').addClass('is-open');
    });
    $('#doc_lightbox').on('click', function () { $(this).removeClass('is-open'); });

    $('#commentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.attr('data-docid', button.data('docid'));
        modal.attr('data-driverid', button.data('driverid'));
        modal.attr('data-doctitle', button.data('doctitle'));
    });

    $('#commentModal').on('hide.bs.modal', function () {
        $(this).removeAttr('data-docid');
    });

    $('.send-msg').click(function () {
        var docid = $('#commentModal').data('docid');
        var driverId = $('#commentModal').data('driverid');
        var docTitle = $('#commentModal').data('doctitle');
        var comment = $('#commentModal').find('#comment').val();
        if (comment == '') {
            alert("{{ trans('lang.add_comment_disapprove') }}");
            return false;
        }

        var url = "{{ route('drivers.documentstatus', ['id', 'type' => 0]) }}";
        url = url.replace('id', docid);

        $.ajax({
            url: url,
            type: "GET",
            data: { docid: docid, comment: comment },
            dataType: 'json',
            success: function (data) {
                $.ajax({
                    url: "{{ route('driver.sendNotification') }}",
                    type: "POST",
                    data: { driver_id: driverId, doc_title: docTitle, _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>
@endsection
