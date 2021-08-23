/* Add here all your JS customizations */
$(document).on('change', '#ensseigne_country, #ensseigne_departement, #ensseigne_ville', function () {
    let $field = $(this)
    let $countryField = $('#ensseigne_country')
    let $departementField = $('#ensseigne_departement')
    let $villeField = $('#ensseigne_ville')
    let $form = $field.closest('form')
    let data = {}
    let target = '#'+ $field.attr('id').replace('ville','quartier').replace('departement','ville').replace('country','departement')
    data[$countryField.attr('name')] = $countryField.val()
    data[$departementField.attr('name')] = $departementField.val()
    data[$field.attr('name')] = $field.val()
    $.post($form.attr('action'), data).then(function (data){
        let $input = $(data).find(target)
        $(target).replaceWith($input)
    })
})

/*$(document).ready(function(){
    $("#ensseigne_country, #ensseigne_departement, #ensseigne_ville").on('change', () => {
        let $field = $(this);
        let $form = $field.closest('form')
        let data = {}

        data[$field.atrr('name')] = $field.val()
        alert('cool')
    })
})*/