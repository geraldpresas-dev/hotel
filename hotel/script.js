// script.js - client side code

var checkIn = document.getElementById('check_in');
var checkOut = document.getElementById('check_out');
var dateError = document.getElementById('dateError');

// 1. Register form: check the passwords before submitting
var registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        var pass = document.getElementById('password').value;
        var confirm = document.getElementById('confirm').value;
        var msg = document.getElementById('errorMsg');

        if (pass.length < 6) {
            msg.textContent = 'Password must be at least 6 characters.';
            e.preventDefault();
        } else if (pass !== confirm) {
            msg.textContent = 'Passwords do not match.';
            e.preventDefault();
        }
    });
}

// 2. Search form and booking form: check-out must be after check-in
if (checkIn && checkOut) {
    function checkDates() {
        if (checkOut.value <= checkIn.value) {
            dateError.textContent = 'Check-out must be after check-in.';
            return false;
        }
        dateError.textContent = '';
        return true;
    }

    // check-out can't be earlier than check-in
    checkIn.addEventListener('change', function () {
        checkOut.min = checkIn.value;
        checkDates();
    });
    checkOut.addEventListener('change', checkDates);

    var forms = [document.getElementById('searchForm'), document.getElementById('bookForm')];
    forms.forEach(function (form) {
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!checkDates()) {
                    e.preventDefault();
                }
            });
        }
    });
}

// 3. Booking page: show nights, total price, and if the room is free
var bookForm = document.getElementById('bookForm');
if (bookForm) {
    var price = parseFloat(bookForm.dataset.price);
    var roomId = bookForm.dataset.room;
    var nightsText = document.getElementById('nightsText');
    var totalText = document.getElementById('totalText');
    var availMsg = document.getElementById('availMsg');

    function updateBooking() {
        var d1 = new Date(checkIn.value);
        var d2 = new Date(checkOut.value);
        var nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));

        if (isNaN(nights) || nights < 1) {
            nightsText.textContent = '-';
            totalText.textContent = '-';
            availMsg.textContent = '';
            return;
        }

        nightsText.textContent = nights;
        totalText.textContent = 'P' + (nights * price).toLocaleString('en-US', { minimumFractionDigits: 2 });

        // ask the server if the room is free (AJAX)
        var url = 'check_availability.php?room_id=' + roomId +
                  '&check_in=' + checkIn.value + '&check_out=' + checkOut.value;

        fetch(url)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.valid) {
                    availMsg.textContent = '';
                } else if (data.available) {
                    availMsg.innerHTML = '<span class="text-success">Room is available on these dates.</span>';
                } else {
                    availMsg.innerHTML = '<span class="text-danger">Room is NOT available on these dates.</span>';
                }
            });
    }

    checkIn.addEventListener('change', updateBooking);
    checkOut.addEventListener('change', updateBooking);
    updateBooking();
}
