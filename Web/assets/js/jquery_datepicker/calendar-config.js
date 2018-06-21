$().ready(function() {
    $('.pUpDate, .dOffDate').datepicker({
        numberOfMonths: 2,
        showButtonPanel: true,
        beforeShow: customRange,
        dateFormat: 'dd-mm-yy',
        showOn: 'both',
        buttonImage: '../img/calendar_icon.png',
        buttonImageOnly: true,
        buttonText: 'Afficher le calendrier'
    });
$('.pUpDate2, .dOffDate2').datepicker({
        numberOfMonths: 2,
        showButtonPanel: true,
        beforeShow: customRange,
        dateFormat: 'dd-mm-yy',
        showOn: 'both',
        buttonImage: 'img/calendar.png',
        buttonImageOnly: true,
        buttonText: 'Afficher le calendrier'
    });
});
function customRange(a) {
    var b = new Date();
    var c = new Date(b.getFullYear(), b.getMonth(), b.getDate());
    if (a.id == 'DropoffDate') {
        if ($('.pUpDate').datepicker('getDate') != null) {
            c = $('.pUpDate').datepicker('getDate');
        }
    }
    return {
        minDate: c
    }
}