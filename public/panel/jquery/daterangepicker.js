$(".datepicker").datepicker({
    minDate: 0,
    numberOfMonths: [12,1],
    beforeShowDay: function(date) {
        var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input1").val());
        var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input2").val());
        return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "dp-highlight" : ""];
    },
    onSelect: function(dateText, inst) {
        var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input1").val());
        var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#input2").val());
        if (!date1 || date2) {
            $("#input1").val(dateText);
            $("#input2").val("");
            $(this).datepicker();
        } else {
            $("#input2").val(dateText);
            $(this).datepicker();
        }
    }
});