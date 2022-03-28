$(function (e) {

    // ready strict
    let err = $('div.log-err')
    let warn = $('div.log-warn')

    let eye = $('div.pass button.eye')
    let pass = $('div.pass input')
    let ipass = eye.children('i.bi')
    let submit = $('div.prompt-box button')

    let passHide = () => {

        pass.attr('type', 'password')
        ipass.removeClass('activate')
        ipass.removeClass('bi-eye')
        ipass.addClass('bi-eye-slash')
    }

    eye.on('click', function() {

        ipass.toggleClass('activate')
        if (!ipass.hasClass('activate')) {

            pass.attr('type', 'password')
            ipass.removeClass('bi-eye')
            ipass.addClass('bi-eye-slash')
        } else {

            pass.attr('type', 'text')
            ipass.removeClass('bi-eye-slash')
            ipass.addClass('bi-eye')
            setTimeout(passHide, 6e2)
        }
    })
    eye.on('focusout', passHide)

    submit.on('click', function () {

        err.removeClass('d-none')
    })
})
