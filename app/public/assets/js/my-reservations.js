document.addEventListener('DOMContentLoaded', () => {

    window.openCancelModal = function (id) {
        if (!id) return;

        const confirmed = confirm('Cancel this reservation?');

        if (confirmed) {
            window.location.href = `/reservations/cancel?id=${id}&confirm=1`;
        }
    };

});
