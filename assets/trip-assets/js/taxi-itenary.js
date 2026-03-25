$(document).ready(function () {
    // Hotel rating selection
    function selectRating(selectedBtn) {
        // Get all rating buttons within the same group
        const buttons = $(selectedBtn).closest('.d-flex').find('.rating-btn');

        // Remove 'active' and 'btn-theme' class from all buttons
        buttons.removeClass('active btn-theme');

        // Add 'active' and 'btn-theme' class to the selected button
        $(selectedBtn).addClass('active btn-theme');
    }

    // Make the selectRating function global
    window.selectRating = selectRating;

    // Review Modal Functionality
    $('#reviewBtn').click(function () {
        $('#reviewModal').modal('show');
    });

    // Star Rating Functionality
    $('.star-item').hover(
        function () {
            var rating = $(this).data('rating');
            highlightStars(rating);
        },
        function () {
            var currentRating = $('#rating-value').val();
            highlightStars(currentRating);
        }
    );

    $('.star-item').click(function () {
        var rating = $(this).data('rating');
        $('#rating-value').val(rating);
        highlightStars(rating);
    });

    function highlightStars(rating) {
        $('.star-item').each(function () {
            if ($(this).data('rating') <= rating) {
                $(this).removeClass('far').addClass('fas text-warning');
            } else {
                $(this).removeClass('fas text-warning').addClass('far');
            }
        });
    }

    // Form submission
    $('#submitReview').click(function () {
        // Get form values
        var rating = $('#rating-value').val();
        var issueCategory = $('#issueCategory').val();
        var comments = $('#reviewComments').val();

        // Validate form
        if (rating === '0') {
            alert('Please select a star rating.');
            return;
        }

        // For demonstration, show what would be submitted
        console.log('Submitting review:');
        console.log('Rating: ' + rating);
        console.log('Issue Category: ' + issueCategory);
        console.log('Comments: ' + comments);

        // Here you would normally send this data to the server
        // $.ajax({...});

        // Show success message
        alert('Thank you for your feedback!');

        // Close modal and reset form
        $('#reviewModal').modal('hide');
        $('#reviewForm')[0].reset();
        highlightStars(0);
    });
});
