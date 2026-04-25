// Example: Simple form validation for date inputs in book_car.php
document.addEventListener('DOMContentLoaded', function () {
    const bookingForm = document.querySelector('form');

    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('Error: Start Date cannot be after End Date.');
            }
        });
    }

    // Confirmation prompt for deleting a user (used in manage_users.php)
    const deleteButtons = document.querySelectorAll('.btn-danger');

    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const confirmDelete = confirm('Are you sure you want to delete this user?');
            if (!confirmDelete) {
                e.preventDefault();
            }
        });
    });
});
