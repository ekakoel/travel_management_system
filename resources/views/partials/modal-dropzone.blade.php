<a href="#" data-toggle="modal" data-target="#promo-price-include-{{ $tour->id }}">
    <button class="btn btn-secondary" ><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Image to Gallery</button>
</a>
<div class="modal fade" id="promo-price-include-{{ $tour->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="card-box">
        <div class="card-box-title text-left">
          <div class="title">
            <i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i> 
            {{ $tour->name." (".$tour->code.')' }}
          </div>
        </div>
        <div class="content">
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
        <div class="card-box-footer text-right">
          <a href="{{ route('view.detail-tour-admin',$tour->id) }}">
              <button type="button" class="btn btn-primary">
                <i class="icon-copy dw dw-diskette1"></i> @lang('messages.Save')
              </button>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

