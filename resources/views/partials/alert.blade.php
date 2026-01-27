@if (isset($messages) && is_array($messages) && count($messages) > 0)
    <div class="alert-error-code">
        <div class="alert alert-{{ $type }}">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
            <ul>
                @foreach ($messages as $message)
                    <li>
                        <div class="alert-text-code">
                            <i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>
                            {!! $message !!}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif