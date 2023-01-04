var $ = jQuery.noConflict();

$(document).ready(() => {

    axios({
        url: "wp-json/gth/model/get_form_where_user",
        method: "GET",
        headers: {
            "X-WP-Nonce": wp_rest.nonce,
        },
    })
        .then((response) => {
            const showCookie = Cookies.get('show');
            //alert(JSON.stringify(showCookie));

            if(showCookie && showCookie == 1) {
                return;
            }
            
            console.log(JSON.stringify(response.data));

            var data = response.data;
            var result = data ? data.length < 3 : false;

            if (result) {
                Swal.fire({
                    icon: "warning",
                    //title: "Atenção",
                    html: "<div class=\"gth-justify\">Olá, para uma melhor experiência de uso desta plataforma, convidamos você a preencher os dados complementares de seu cadastro.</div>"
                        + "<br><div class=\"gth-justify\">Você pode clicar em \"Preencher perfis\" e iniciar o preenchimento agora:</div>",
                    showCancelButton: true,
                    confirmButtonColor: "#04A5AC",
                    confirmButtonText: "Preencher perfis",
                    cancelButtonText: "Preencher depois"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = './minha-conta/profile/';
                    }

                    Cookies.set('show', '1', { expires: 1, path: '', sameSite: 'None', secure: false })
                    //document.cookie = "show=1; SameSite=None; Secure";
                })
            }
        })
        .catch((error) => {
            /*
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Alguma coisa deu errado!",
                confirmButtonColor: "#04A5AC",
            });
            */
            console.log(error);
        });
});