@if (isset($error) && is_array($error) && count($error) > 0)
    <div class="alert-msg">
        <span class="close-btn" onclick="this.parentElement.style.display='none';"><i class="fa fa-times" aria-hidden="true"></i></span>
        <ul>
            @foreach ($error as $error_msg)
                <li>{!! $error_msg !!}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (isset($success) && is_array($success) && count($success) > 0)
    <div class="success-msg">
        <span class="close-btn" onclick="this.parentElement.style.display='none';"><i class="fa fa-times" aria-hidden="true"></i></span>
        <ul>
            @foreach ($success as $success_msg)
                <li>{!! $success_msg !!}</li>
            @endforeach
        </ul>
    </div>
@endif