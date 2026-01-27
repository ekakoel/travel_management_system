<template>
    <div>
        <div class="form-group">
            <label for="status" class="form-label">Type <span>*</span></label>
            <select class="custom-select">
                <option value="Bride">Bride</option>
                <option value="Invitations">Invitations</option>
            </select>
        </div>

        <div class="form-group">
            <label for="guest_name" class="form-label">Guest Name</label>
            <input type="text" name="guest_name" class="form-control" placeholder="Guest Name" required>
        </div>
        <div class="form-group">
            <label for="number_of_guests" class="form-label">Number of Guests</label>
            <input type="text" name="number_of_guests" class="form-control" placeholder="Number of Guest" required>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedStatus: "{{ $dinner_venue->status }}",
            venueName: "{{ $dinner_venue->name }}",
            statuses: ['Option 1', 'Option 2', 'Option 3'] // Ganti dengan daftar status yang sesuai
        }
    }
}
</script>
