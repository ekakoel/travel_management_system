<div class="form-group">
    <label for="wedding_planner_id">@lang("messages.Select Wedding Planner") <span>*</span></label>
    <select id="wedding_planner_id" class="custom-select @error('wedding_planner_id') is-invalid @enderror" name="wedding_planner_id" required>
        <option selected value="">@lang('messages.Wedding Planner')</option>
        @foreach ($wedding_planners as $wedding_planner)
            <option value="{{ $wedding_planner->id }}" data-slot="{{ $wedding_planner->slot }}" data-duration="{{ $wedding_planner->duration }}">{{ $wedding_planner->name }}</option> // $wedding_planner->slot = date (Y-m-d) atau Checkin date
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="slot">Select Date <span>*</span></label>
    <select id="slot" class="custom-select @error('slot') is-invalid @enderror" name="slot" required>
        <option selected value="">Select one</option>
        @for ($i = 0; $i < $duration; $i++)
            <option value="{{ $slot }}">{{ date('Y-m-d',strtotime('+1 days',strtotime($slot))) }}</option>
        @endfor
    </select>
</div>