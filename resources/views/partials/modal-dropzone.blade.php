<button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#tour-gallery-upload-{{ $tour->id }}">
    <i class="fa fa-plus" aria-hidden="true"></i>
    Add Image to Gallery
</button>

<div class="modal fade backend-modal tour-detail-modal tour-gallery-upload-modal" id="tour-gallery-upload-{{ $tour->id }}" tabindex="-1" role="dialog" aria-labelledby="tour-gallery-upload-title-{{ $tour->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="backend-modal__header">
                <div>
                    <span class="backend-section-header__label">Gallery Upload</span>
                    <h5 id="tour-gallery-upload-title-{{ $tour->id }}">{{ $tour->name }} ({{ $tour->code }})</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('messages.Close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="backend-modal__body">
                <form
                    action="{{ route('func.tour-gallery.upload') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="dropzone tour-gallery-dropzone"
                    data-tour-id="{{ $tour->id }}"
                >
                    @csrf
                    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                    <div class="dz-message">Drop files here or click to upload</div>
                </form>
            </div>
            <div class="backend-modal__footer">
                <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-button backend-button-primary">
                    <i class="fa fa-check" aria-hidden="true"></i>
                    @lang('messages.Save')
                </a>
                <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">
                    @lang('messages.Close')
                </button>
            </div>
        </div>
    </div>
</div>
