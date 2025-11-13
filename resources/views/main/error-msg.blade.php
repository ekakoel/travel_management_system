@if (count($errors) > 0)
    <div class="alert-error-code">
        <div class="alert alert-danger w3-animate-top">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <ul>
                @foreach ($errors->all() as $error)
                <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{{ $error }}</div></li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@if (\Session::has('success'))
    <div class="alert-error-code">
        <div class="alert alert-success w3-animate-top">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <ul>
                <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('success') !!}</div></li>
            </ul>
        </div>
    </div>
@endif
@if ($bookingcode_status == "Invalid")
    <div class="alert-error-code w3-animate-top">
        <div class="alert alert-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <ul>
                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Invalid Code')</div></li>
            </ul>
        </div>
    </div>
@endif
@if ($bookingcode_status == "Expired")
    <div class="alert-error-code w3-animate-top">
        <div class="alert alert-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <ul>
                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Expired Code')</div></li>
            </ul>
        </div>
    </div>
@endif
@if ($bookingcode_status == "Used")
    <div class="alert-error-code w3-animate-top">
        <div class="alert alert-danger">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <ul>
                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Used Code')</div></li>
            </ul>
        </div>
    </div>
@endif