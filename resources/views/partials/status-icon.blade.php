
@if ($status == "Rejected")
    <div class="status-rejected">{{ $status }}</div>
@elseif ($status == "Invalid")
    <div class="status-invalid">{{ $status }}</div>
@elseif ($status == "Active")
    <div class="status-active">{{ $status }}</div>
@elseif ($status == "Waiting")
    <div class="status-waiting">{{ $status }}</div>
@elseif ($status == "Draft")
    <div class="status-draft">{{ $status }}</div>
@elseif ($status == "Archived")
    <div class="status-archived">{{ $status }}</div>
@else
@endif