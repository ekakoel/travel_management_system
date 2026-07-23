<input type="hidden" name="name" value="{{ $transport->name }}">
<input type="hidden" name="type" value="{{ $transport->type }}">
<input type="hidden" name="brand" value="{{ $transport->brand }}">
<input type="hidden" name="description" value="{{ $transport->description }}">
<input type="hidden" name="include" value="{{ $transport->include }}">
<input type="hidden" name="additional_info" value="{{ $transport->additional_info }}">
<input type="hidden" name="cancellation_policy" value="{{ $transport->cancellation_policy }}">
<input type="hidden" name="capacity" value="{{ $transport->capacity }}">
<input type="hidden" name="status" value="{{ $transport->status }}">
<input type="hidden" name="author" value="{{ Auth::id() }}">
<input type="hidden" name="page" value="edit-transport-gallery">
<input type="hidden" name="initial_state" value="{{ $transport->status }}">
