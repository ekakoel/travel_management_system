<div class="card-box">
    <div class="card-box-form-title">
        <div class="form-title"><i class="icon-copy fi-page-edit"></i> @lang("messages.Create Wedding Planner")</div>
    </div>
    <div class="cardbox-content">
        <form id="add-wedding-planner" action="/fadd-wedding-planner" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="wedding_name">@lang("messages.Wedding Location")</label>
                        <input readonly type="text" name="wedding_name" class="form-control input-icon @error('wedding_name') is-invalid @enderror" placeholder="@lang("messages.Wedding Location")" value="{{ $hotel->name }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="groom_name">@lang("messages.Groom's Name") <span> *</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fi-torso"></i></span>
                            <input type="text" name="groom_name" id="groom_name" class="form-control input-icon @error('groom_name') is-invalid @enderror" placeholder="@lang("messages.Groom's Name")" value="{{ old('groom_name') }}" required>
                        </div>
                        @error('groom_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="bride_name">@lang("messages.Bride's Name") <span> *</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fi-torso-female"></i></span>
                            <input type="text" name="bride_name" id="bride_name" class="form-control input-icon @error('bride_name') is-invalid @enderror" placeholder="@lang("messages.Bride's Name")" value="{{ old('bride_name') }}" required>
                        </div>
                        @error('bride_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="checkin">@lang("messages.Check-in") <span> *</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fi-calendar"></i></span>
                            <input name="checkin" type="text" id="checkin" class="form-control input-icon date-picker @error('checkin') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('checkin') }}" required>
                        </div>
                        @error('checkin')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="checkout">@lang("messages.Check-out") <span> *</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fi-calendar"></i></span>
                            <input name="checkout" type="text" id="checkout" class="form-control input-icon date-picker @error('checkout') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('checkout') }}" required>
                        </div>
                        @error('checkout')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="wedding_date">@lang("messages.Wedding Date") <span> *</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fi-calendar"></i></span>
                            <input name="wedding_date" type="text" id="wedding-date" class="form-control input-icon date-picker @error('wedding_date') is-invalid @enderror" placeholder="Select Date" type="text" value="{{ old('wedding_date') }}" required>
                        </div>
                        @error('wedding_date')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="number_of_invitations">@lang('messages.Number of Invitations') <span> *</span></label>
                        <div class="btn-icon">
                            <span><i class="icon-copy fi-torsos-all"></i></span>
                            <input name="number_of_invitations" type="number" min="1" id="number_of_invitations" class="form-control input-icon @error('number_of_invitations') is-invalid @enderror" placeholder="Insert number of invitations" type="text" value="{{ old('number_of_invitations') }}" required>
                        </div>
                        @error('number_of_invitations')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <input type="hidden" id="hotel_id" name="hotel_id" value="{{ $hotel->id }}">
            <input type="hidden" id="page_url" name="page_url">
            <input type="hidden" name="key" value="1">
        </form>
    </div>
    <div class="card-box-footer">
        <button type="submit" form="add-wedding-planner" class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> @lang("messages.Create")</button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var currentPageUrl = window.location.href;
        document.getElementById('page_url').value = currentPageUrl;
    });
</script>

