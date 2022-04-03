export default class Activity extends Object {

    static jessieQuery;
    static pass;

    constructor() {

        super()
    }

    static Main() {

        let pass = document.querySelector("input[type=\"password\"]#passwordInput")
        let eye = document.querySelector("div.eye i.material-icons.visibility")
        eye.addEventListener("click", this.eyeOnClickListener.bind(this))
        eye.visibility = false

        this.jessieQuery.styleInsertRule(".material-icons.visibility:before { color: var(--var-grey); }")
        this.jessieQuery.styleInsertRule(".material-icons.visibility_off.activate:before { color: var(--var-black); }")
        this.pass = pass
    }

    static eyeOnClickListener(e) {

        let target = e && "target" in e && e.target
        let visibility = "visibility" in target ? target.visibility : false

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            if (!visibility) {

                setTimeout((function () {

                    target.visibility = this.eyeVisibleOff(e)

                }).bind(this), 6e2)

                target.visibility = this.eyeVisible(e)

            } else {

                target.visibility = this.eyeVisibleOff(e)
            }
        }
    }

    static eyeVisible(e) {

        let target = e && e.target

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            target.classList.remove("visibility")
            target.classList.add("visibility_off")
            target.classList.add("activate")
        }

        if (Element.prototype.isPrototypeOf(this.pass))
            this.pass.setAttribute("type", "text")

        return true
    }

    static eyeVisibleOff(e) {

        let target = e && e.target

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            target.classList.remove("visibility_off")
            target.classList.remove("activate")
            target.classList.add("visibility")
        }

        if (Element.prototype.isPrototypeOf(this.pass))
            this.pass.setAttribute("type", "password")

        return false
    }
}