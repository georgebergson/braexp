jQuery(document).ready(() => {
    var func_autocomplete = (evt) => {

        console.log("@" + evt.target.value);

        var cnpj = evt.target.value.replace(/\D/g, '');

        if (cnpj.length < 14) {
            return;
        }

        // states
        //
        var states = {
            'AC': 'Acre',
            'AL': 'Alagoas',
            'AP': 'Amapá',
            'AM': 'Amazonas',
            'BA': 'Bahia',
            'CE': 'Ceará',
            'DF': 'Distrito Federal',
            'ES': 'Espírito Santo',
            'GO': 'Goiás',
            'MA': 'Maranhão',
            'MT': 'Mato Grosso',
            'MS': 'Mato Grosso do Sul',
            'MG': 'Minas Gerais',
            'PA': 'Pará',
            'PB': 'Paraíba',
            'PR': 'Paraná',
            'PE': 'Pernambuco',
            'PI': 'Piauí',
            'RJ': 'Rio de Janeiro',
            'RN': 'Rio Grande do Norte',
            'RS': 'Rio Grande do Sul',
            'RO': 'Rondônia',
            'RR': 'Roraima',
            'SC': 'Santa Catarina',
            'SP': 'São Paulo',
            'SE': 'Sergipe',
            'TO': 'Tocantins'
        }

        // config fields
        //
        var fields = {
            'text-2': jQuery('#input-10'),
            'text-3': jQuery('#input-13'),
            'url-1': jQuery('#input-16'),
            'text-4': jQuery('#input-19'),
            'text-5': jQuery('#input-22'),
            'text-6': jQuery('#input-25'),
            'text-7': jQuery('#input-28'),
            'text-8': jQuery('#input-31'),
            'text-9': jQuery('#input-39'),
        }

        // rest service
        //
        var getUrl = window.location;
        var baseUrl = getUrl.protocol + "//" + getUrl.host + "/"

        jQuery.ajax({
            url: baseUrl + 'cnpj/' + cnpj,
            type: 'GET',
            beforeSend: (xhr) => {
                xhr.setRequestHeader('Access-Control-Allow-Origin', '*')
                xhr.setRequestHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept')
            },
        }).done((data) => {

            console.log(JSON.stringify(data))

            var endereco = ((data['ds_tipo_end'] || '') + ' ' + (data['no_end'] || '')).trim();
            var numero = ((data['nu_end'] || '') + '').trim();

            if (endereco != '' && numero != '') {
                endereco += ', ' + numero;
            }

            // data fields
            //
            var insert = {
                'text-2': data['no_razao_social'] || '',
                'text-3': data['no_fantasia'] || '',
                'url-1': '',
                'text-4': (data['nu_ddd_1'] || '') + '' + (data['nu_tel_1'] || ''),
                'text-5': endereco,
                'text-6': data['ds_compl'] || '',
                'text-7': data['no_bairro'] || '',
                'text-8': data['no_municipio'] || '',
                'text-9': data['nu_cep'] || '',
            }

            for (var [key, value] of Object.entries(fields)) {
                if ((insert[key] + '').trim() != '') {
                    value.val(insert[key]);
                    //data-mask-raw-value
                }
            }

            jQuery('#input-34').val(states[data['co_uf']]).change();
        });
    }
    
    /*
    jQuery('#input-7')
        .keyup(func_autocomplete);
    */
});