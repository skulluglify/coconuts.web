$(function (e) {

    // ready strict
    let err = $('div.log-err')
    let warn = $('div.log-warn')

    let eye = $('div.pass button.eye')
    let pass = $('div.pass input')
    let logoEye = eye.children('i.bi')
    let submit = $('div.prompt-box button.sign-in')

    let passHide = () => {

        pass.attr('type', 'password')
        logoEye.removeClass('activate')
        logoEye.removeClass('bi-eye')
        logoEye.addClass('bi-eye-slash')
    }

    eye.on('click', function() {

        logoEye.toggleClass('activate')
        if (!logoEye.hasClass('activate')) {

            pass.attr('type', 'password')
            logoEye.removeClass('bi-eye')
            logoEye.addClass('bi-eye-slash')
        } else {

            pass.attr('type', 'text')
            logoEye.removeClass('bi-eye-slash')
            logoEye.addClass('bi-eye')
            setTimeout(passHide, 6e2)
        }
    })
    eye.on('focusout', passHide)

    submit.on('click', function () {

        err.removeClass('d-none')
    })
})
