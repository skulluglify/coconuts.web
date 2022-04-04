export default class CheckAuto {

    static jessieQuery;

    static Main() {

        this.InitCheckAuto()
    }

    static InitCheckAuto() {

        let checkNodes = document.querySelectorAll("div.check")

        if (checkNodes.length > 0)
            Array.from(checkNodes).forEach((function (node) {

                node.addEventListener("click", this.checkOnCLickListener.bind(this))
            }).bind(this))
    }

    static checkOnCLickListener(e) {

        let target = e && "target" in e && e.target

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let radioInput = target.querySelector("input[type=\"radio\"]")

            if (radioInput && HTMLInputElement.prototype.isPrototypeOf(radioInput)) {

                radioInput.checked = true
                radioInput.click()
            }
        }
    }
}