{{-- Modal untuk Detail Room --}}
<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalLabel">Room Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="roomCoverImg" src="" alt="Room Image" class="img-fluid mb-3">
                <h4 id="roomName"></h4>
                <p><strong>Room Type:</strong> <span id="roomType"></span></p>
                <p><strong>Bed Type:</strong> <span id="bedType"></span></p>
                <p><strong>Adults:</strong> <span id="guestAdult"></span>, <strong>Children:</strong> <span id="guestChild"></span></p>
                <p><strong>Size:</strong> <span id="roomSize"></span> m²</p>
                <p><strong>Amenities:</strong></p>
                <p id="roomAmenities"></p>
                <p><strong>Description:</strong></p>
                <p id="roomDescription"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
