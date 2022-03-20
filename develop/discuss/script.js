$(() => {
    let userIcon = $(".user-icon")
    let userBar = $(".user-bar")
    let popOptions = {
        trigger: "focus",
        placement: "bottom",
        container: "body",
        content: () => userBar.html(),
        html: true
    }
    userIcon.popover(popOptions)
})