$(document).ready(function () {
    init_datepicker();
    init_selectpicker();
    _validate_form($('.form-record-payment'), {
        amount: 'required',
        date: 'required',
        paymentmode: 'required'
    });
});