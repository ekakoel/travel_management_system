document.addEventListener('DOMContentLoaded', function () {
    const roomModal = document.getElementById('roomModal');

    if (!roomModal) {
        return;
    }

    const roomModalImage = document.getElementById('roomModalImage');
    const roomModalTitle = document.getElementById('roomModalTitle');

    roomModal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;
        const imageSrc = card.getAttribute('data-image');
        const roomName = card.getAttribute('data-room-name');

        roomModalImage.src = imageSrc;
        roomModalTitle.textContent = roomName;
    });
});
