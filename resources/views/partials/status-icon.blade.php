
@if ($status == "Rejected")
    <div class="status-rejected"></div>
@elseif ($status == "Invalid")
    <div class="status-invalid"></div>
@elseif ($status == "Active")
    <div class="status-active"></div>
@elseif ($status == "Waiting")
    <div class="status-waiting"></div>
@elseif ($status == "Draft")
    <div class="status-draft"></div>
@elseif ($status == "Archived")
    <div class="status-archived"></div>
@else
@endif