
<script src="{{ asset('panel/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('panel/script/core.js') }}"></script>
<script src="{{ asset('panel/script/script.min.js') }}"></script>
<script src="{{ asset('panel/script/process.js') }}"></script>
<script src="{{ asset('panel/script/layout-settings.js') }}"></script>
<script src="{{ asset('panel/bootstrap-touchspin/jquery.bootstrap-touchspin.js') }}"></script>
<script src="{{ asset('panel/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('vendors/scripts/calendar-setting.js') }}"></script>
<script src="{{ asset('assets/dist/pdfreader/pspdfkit.js') }}"></script>
<script src="{{ asset('panel/slick/slick.min.js') }}"></script>
<script src="{{ asset('panel/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('panel/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('panel/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('panel/datatables/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('panel/datatables/js/datatable-setting.js') }}"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/frontend/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/counterup/counterup.min.js') }}"></script>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js"></script>
<script src="{{ mix('build/backend/js/app.js') }}" defer></script>
<script>
    $(document).ready(function() {
        $('.numeric-input').on('input', function(event) {
            var sanitizedValue = $(this).val().replace(/[^0-9.]/g, '');
            if (sanitizedValue) {
                var floatValue = parseFloat(sanitizedValue);
                var formattedValue = floatValue.toLocaleString('en-US');
                $(this).val(formattedValue);
            } else {
                $(this).val('');
            }
        });
    });
</script>

<script>
    var phoneInputs = document.getElementsByClassName('phone');
    for (var i = 0; i < phoneInputs.length; i++) {
        phoneInputs[i].addEventListener('input', function (e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,4})(\d{0,3})(\d{0,3})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '');
        });
    }
</script>


<script>
	$(document).ready(function(){
	  $('[data-toggle="tooltip"]').tooltip();   
	});
</script>


<script type="text/javascript">
    $(document).ready(function(){  
		var maxField = 8; 
		var rpr = 1;  
		var pr=1;
		$('#add_room_promo').click(function(){
			if(rpr < maxField){ 
				rpr++;
				pr++;
				$('#dynamic_field_promo').append('<li id="promo'+pr+'" class="m-b-8"><div class="room-container "><div class="row"><div class="col-sm-12"><div class="subtitle">Room '+pr+'</div><button class="btn btn-remove" name="remove" id="'+pr+'" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button></div><div class="col-sm-3"><div class="form-group"><label for="number_of_guests[]">Number of Guests</label><input type="number" min="1" max="4" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="Number of guests" value="{{ old('number_of_guests[]') }}" required>@error('number_of_guests[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-9"><div class="form-group"><label for="guest_detail[]">Guest Name </label><input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="Separate names with commas" value="{{ old('guest_detail[]') }}" required>@error('guest_detail[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-5"><div class="form-group"><label for="special_day[]">Special Day <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="If during your stay there are guests who have special days such as birthdays, aniversaries, and others" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="ex: Birthday" value="{{ old('special_day[]') }}">@error('special_day[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-5"><div class="form-group"><label for="special_date[]">Insert Date</label><input type="text" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yy/mm/dd" value="{{ old('special_date[]') }}">@error('special_date[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-2" style="place-self: padding-bottom: 6px;"><div class="form-group"><label class="p-b-8" for="extra_bed[]">Extra Bed <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="Extra bed is only for children under 12 years old." class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br><input name="extra_bed[]" type="checkbox" value="Yes"></div></div></div></div></li>');  
				}
		});
		$(document).on('click', '.btn-remove', function(){  
			var button_id = $(this).attr("id");   
			$('#promo'+button_id+'').remove();
			pr--;
			rpr--;
		}); 
    });
</script>

<script>
	$(document).ready(function() {
        var today = moment().toDate();
        var oneDayAfter = moment().add(1, 'days').toDate();
        var sevenDaysAfter = moment().add(7, 'days').toDate();
        var nineDaysAfter = moment().add(9, 'days').toDate();
        var defaultDate = moment().add(7, 'days').format('MM-DD-YYYY');
        $("#checkin, #checkout, #departure-time, #arrival-time, #weddingDate, #arrivalFlightDate, #departureFlightDate, #checkIn, #datein, #dateout")
            .datepicker({
                minDate: sevenDaysAfter,
                defaultDate: defaultDate,
				format: 'YYYY-MM-DD',
            });
        $("#arrivalFlightDate, #departureFlightDate, #checkIn, #datein, #dateout").datepicker({
            minDate: oneDayAfter,
			format: 'YYYY-MM-DD',
        });

        $("#checkout").datepicker({
            minDate: nineDaysAfter,
			format: 'YYYY-MM-DD',
        });

        $('input[name="checkincout"]').daterangepicker({
            minDate: sevenDaysAfter,
            opens: 'left',
            autoApply: true,
			format: 'YYYY-MM-DD',
        });

        $('.wedding-date').each(function () {
            $(this).datepicker({
                minDate: sevenDaysAfter,
                defaultDate: defaultDate,
				format: 'YYYY-MM-DD',
            }).datepicker('setDate', defaultDate);
        });

		$('.datetimepicker').each(function () {
            $(this).datetimepicker({
                minDate: sevenDaysAfter,
                defaultDate: defaultDate,
				format: 'YYYY-MM-DD',
            }).datepicker('setDate', defaultDate);
        });
       
       $("#checkincheckout").daterangepicker({
            minDate: today,
            opens: 'left',
            autoApply: true,
			language: 'en',
			format: 'YYYY-MM-DD',
        });

        $("#travel_date").datepicker({
            minDate: oneDayAfter,
            defaultDate: defaultDate,
            locale: 'de',
            format: 'YYYY-MM-DD',
        });
        $('input[name="spk_date"]').daterangepicker({
            singleDatePicker: true,
			autoApply: true,
			autoUpdateInput: true,
			showDropdowns: true,
			 minDate: today,
			language: 'en',
			locale: {
				format: 'YYYY-MM-DD',
			}
        });
        $('input[name="flight_date"]').daterangepicker({
            singleDatePicker: true,
			autoApply: true,
			autoUpdateInput: true,
			showDropdowns: true,
			minDate: today,
			language: 'en',
			locale: {
				format: 'YYYY-MM-DD',
			}
        });
        $('input[name="travel_date"]:not([data-ui-picker])').daterangepicker({
            singleDatePicker: true,
			autoApply: true,
			autoUpdateInput: true,
			showDropdowns: true,
			 minDate: today,
			language: 'en',
			locale: {
				format: 'YYYY-MM-DD',
			}
        });
    });
</script>


<script>
	function searchTable(inputId, tableId, columnIndex) {
	  const input = document.getElementById(inputId);
	  const filter = input.value.toUpperCase();
	  const table = document.getElementById(tableId);
	  const rows = table.getElementsByTagName("tr");
  
	  Array.from(rows).forEach((row) => {
		const cell = row.getElementsByTagName("td")[columnIndex];
		if (cell) {
		  const textValue = cell.textContent || cell.innerText;
		  row.style.display = textValue.toUpperCase().includes(filter) ? "" : "none";
		}
	  });
	}
</script>


<script>
	Dropzone.autoDiscover = false;
	$(".dropzone").dropzone({
		addRemoveLinks: true,
		removedfile: function(file) {
			var name = file.name;
			var _ref;
			return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
		}
	});
</script>
<script>
	Dropzone.autoDiscover = false;
	$(".bannerzone").dropzone({
		addRemoveLinks: true,
		removedfile: function(file) {
			var name = file.name;
			var _ref;
			return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
		}
	});
</script>

<script>
	$(function() {
		var previewImages = function(input, imgPreviewPlaceholder) {
			if (input.files) {
				var filesAmount = input.files.length;
				for (var i = 0; i < filesAmount; i++) {
					var reader = new FileReader();
					reader.onload = function(event) {
						$($.parseHTML('<img>')).attr('src', event.target.result).appendTo(imgPreviewPlaceholder);
					}
					reader.readAsDataURL(input.files[i]);
				}
			}
		};

		$('#cover, #coverPackage, #updateCoverPackage, #desktop_receipt_name, #mobile_receipt_name, #activity_receipt_name, #receipt_name, #images, #profileimg, #banner').on('change', function() {
			var targetDiv;
			switch (this.id) {
				case 'cover':
					targetDiv = 'div.cover-preview-div';
					break;
				case 'coverPackage':
					targetDiv = 'div.cover-package-preview-div';
					break;
				case 'updateCoverPackage':
					targetDiv = 'div.update-cover-package-preview-div';
					break;
				case 'desktop_receipt_name':
					targetDiv = 'div.desktop-receipt-div';
					break;
				case 'mobile_receipt_name':
					targetDiv = 'div.mobile-receipt-div';
					break;
				case 'activity_receipt_name':
					targetDiv = 'div.activity-preview-div';
					break;
				case 'receipt_name':
					targetDiv = 'div.tour-receipt-div';
					break;
				case 'images':
					targetDiv = 'div.images-preview-div';
					break;
				case 'profileimg':
					targetDiv = 'div.profile-preview-div';
					break;
				case 'banner':
					targetDiv = 'div.banner-preview-div';
					break;
			}
			previewImages(this, targetDiv);
		});
	});
</script>
<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('page-loader');
        loader.style.opacity = '0';
        loader.style.transition = 'opacity 0.4s ease';

        setTimeout(() => {
            loader.style.display = 'none';
        }, 400);
    });
</script>


<script>
$(document).ready(function(){
  $(".nav-tabs a").click(function(){
    $(this).tab('show');
  });
});
</script>
<script>
	jQuery(document).ready(function() {
		jQuery('.product-slider').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: true,
			infinite: true,
			speed: 1000,
			fade: true,
			asNavFor: '.product-slider-nav'
		});
		jQuery('.product-slider-nav').slick({
			slidesToShow: 3,
			slidesToScroll: 1,
			asNavFor: '.product-slider',
			dots: false,
			infinite: true,
			arrows: false,
			speed: 1000,
			centerMode: true,
			focusOnSelect: true
		});
		$("input[name='demo3_22']").TouchSpin({
			initval: 1
		});
	});
</script>

<script>
    window.setTimeout(function() {
      $(".info-action").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
      });
    }, 5000);
</script>
