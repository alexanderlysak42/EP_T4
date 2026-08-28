document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.customform');
    if (!form) return;

    var phone = form.querySelector('input[name="phone"]');
    if (!phone) return;

    phone.addEventListener('input', function () {
        var digits = phone.value.replace(/\D/g, '');
        if (digits && digits[0] !== '3') {
            digits = '380' + digits.replace(/^0+/, '');
        }
        phone.value = digits ? '+' + digits : '';
    });
});
