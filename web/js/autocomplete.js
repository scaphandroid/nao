var options = {
    url_list: $('#url-list').attr('href'),
    url_get: $('#url-get').attr('href')
};

$('#nao_platformbundle_especeNomVern_nomVern').autocompleter(options);
$('#nao_platformbundle_observation_especeNomVern').autocompleter(options);