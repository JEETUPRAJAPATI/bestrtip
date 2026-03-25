

$(document).ready(function () {
    initBackButtonHandler();
    initBookNowButtonHandler();

    $("#datepicker-start, #datepicker-end").datepicker({
        firstDay: 1,
        showOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd.mm.yy"
    });

    $(".date").mousedown(function () {
        $(".ui-datepicker").addClass("active");
    });

    $(".ui-datepicker").on("click", function (e) {
        e.stopPropagation();
    });

    $(".room-counter .counter-btn").on("click", function (e) {
        e.preventDefault();

        const $btn = $(this);
        const $counter = $btn.closest(".room-counter");
        const $value = $counter.find(".counter-value");
        let count = parseInt($value.text());

        if ($btn.data("action") === "increase") {
            count++;
        } else if ($btn.data("action") === "decrease" && count > 1) {
            count--;
        }

        $value.text(count);
    });
});

// book-now modal function code : start
// Handling the back button function(otp veri.) - code
const initBackButtonHandler = () => {
    let from = 'user';
    $('label[for="agent"]').click(() => from = 'agent');
    $('label[for="step1"]').click(() => from = 'user');
    $('#back-btn').click(() => {
        $('#' + (from === 'agent' ? 'agent' : 'step1')).prop('checked', true);
    });
};
// Handling the Book Now button function - code
const initBookNowButtonHandler = () => {
    const bookNowBtn = document.getElementById("book-now-btn");
    const modalToggle = document.getElementById("modal-toggle");
    if (bookNowBtn && modalToggle) {
        bookNowBtn.addEventListener("click", function () {
            modalToggle.checked = true;
        });
    }
};

// Add active class to first date button
document.addEventListener('DOMContentLoaded', function () {
    const dateButtons = document.querySelectorAll('.date-btn');
    dateButtons[0].classList.add('active');

    // Add click event to all date buttons
    dateButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Remove active class from all buttons
            dateButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
        });
    });
});

document.querySelectorAll('.flight-card').forEach(card => {
    card.addEventListener('click', function () {
        // Reset all cards
        document.querySelectorAll('.flight-card').forEach(c => {
            c.style.border = '1px solid #e0e0e0';
            c.querySelector('.selection-indicator').style.display = 'none';
        });

        // Select this card
        this.style.border = '2px solid #0275d8';
        this.querySelector('.selection-indicator').style.display = 'flex';
    });
});

document.getElementById('mobile-menu-button').addEventListener('click', function () {
    const menu = document.getElementById('mobile-menu');
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
});
