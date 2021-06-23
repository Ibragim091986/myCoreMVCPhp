
$('.alias-name-in').on('keyup', function() {
    $('.alias-name-out').val(rus_to_latin($(this).val()));
});

$('.alias-tire-name-in').on('keyup', function() {
    setAliaseTire();
});

$('.alias-tire-width-in').on('keyup', function() {
    setAliaseTire();
});
$('.alias-tire-width-in').on('change', function() {
    setAliaseTire();
});

$('.alias-tire-proportion-in').on('keyup', function() {
    setAliaseTire();
});
$('.alias-tire-proportion-in').on('change', function() {
    setAliaseTire();
});

$('.alias-tire-diameter-in').on('keyup', function() {
    setAliaseTire();
});
$('.alias-tire-diameter-in').on('change', function() {
    setAliaseTire();
});


function setAliaseTire() {
    let aliase = '';
    let name = $('.alias-tire-name-in').val();
    let width = $('.alias-tire-width-in').val();
    let proportion = $('.alias-tire-proportion-in').val();
    let diameter = $('.alias-tire-diameter-in').val();

    if(width) aliase = aliase + width;

    if(aliase && proportion) aliase = aliase + 'x' + proportion;
    else if(proportion) aliase = proportion;

    if(aliase && diameter) aliase = aliase + '_R' + diameter;
    else if(diameter) aliase = 'R' + diameter;

    if(aliase && name) aliase = aliase + '_' + name;
    else if(name) aliase = name;

    $('.alias-tire-name-out').val(rus_to_latin(aliase));
}


function rus_to_latin ( str ) {

    str = str.replace(/ +/g, ' ').trim().replace(/ +/g, '_').replace(/[\+-.,/\\]/g, '_').toLowerCase();

    let ru = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
        'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
        'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
        'щ': 'shch', 'ы': 'i', 'э': 'e', 'ю': 'u', 'я': 'ya'
    }, n_str = [];

    str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i');

    for ( let i = 0; i < str.length; ++i ) {
        n_str.push(
            ru[ str[i] ]
            || ru[ str[i].toLowerCase() ] == undefined && str[i]
            || ru[ str[i].toLowerCase() ].replace(/^(.)/, function ( match ) { return match.toUpperCase() })
        );
    }

    return n_str.join('');
}

/*let datepicker_conf = {
    bootcssVer: 3,
    format: "dd.mm.yyyy",
    minuteStep: 120,
    showMeridian: false,
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left",
    language: 'ru',
    datetimepickerHours: 'false',
    beforeShowDecade: 'classes',
};*/

let datepicker_conf = {
    language: 'ru',
    format: "dd.mm.yyyy",
    autoclose: true,
    // todayBtn: true
};

$(".form_datetime").datepicker(datepicker_conf);

$("body #tableFilter").on("pjax:end", function() {

    $(".form_datetime").datepicker(datepicker_conf);
});