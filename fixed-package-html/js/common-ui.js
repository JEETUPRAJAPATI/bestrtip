jQuery(document).ready(function(){

    if (jQuery(".accordion-block").length) {
        //Active first child 
        if(jQuery('.accordion-list').hasClass('first-active')){
            jQuery('.first-active').children('.accordion-main').addClass('active');
            jQuery('.first-active').children('.accordion-main').children('span').text('—');

        }
        jQuery('.accordion-main').click(function() {
            if (jQuery(this).hasClass('active')) {
                jQuery(this).removeClass('active');
                jQuery(this).next('.accordion-expand').slideUp();
                jQuery(this).children('span').text('+');
            } else {
                jQuery(this).addClass('active');
                jQuery(this).next('.accordion-expand').slideDown();
                jQuery(this).children('span').text('—');
            }
        });
    }

    // var enabledDates = new Array('2024-08-12', '2024-08-16', '2024-08-18', '2024-08-30', '2024-08-05', '2024-08-10');
    console.log(' enable dates', enabledDates);
    $(function() {
        $("#newOrderDates").datepicker({
        todayHighlight: true,
        dateFormat: 'yy-mm-dd',
        multidate: true,
        startDate: new Date(),
        beforeShowDay: enableAllTheseDays
        });
    });
    
    function enableAllTheseDays(date) {
        var sdate = moment(date).format('YYYY-MM-DD');
        if ($.inArray(sdate, enabledDates) !== -1) {
            return [true];
        }
        return [false];
    }

});

