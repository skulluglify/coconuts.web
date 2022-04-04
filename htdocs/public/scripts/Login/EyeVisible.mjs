export default class EyeVisible extends Object {

    static jessieQuery;

    constructor() {

        super()
    }

    static InitEyeVisible() {

        let pass = document.querySelectorAll("div.pass")

        if (pass.length > 0) {

            Array.from(pass).forEach((function (node) {

                let eye = node.querySelector("div.eye")
                eye.addEventListener("click", this.eyeOnClickListener.bind(this))
                eye.visibility = false
            }).bind(this))
        }

        this.jessieQuery.styleInsertRule(".material-icons.visibility_off:before { color: var(--var-grey); }")
        this.jessieQuery.styleInsertRule(".material-icons.visibility.activate:before { color: var(--var-black); }")
    }

    static eyeOnClickListener(e) {

        let target = e && "target" in e && e.target
        let visibility = "visibility" in target ? target.visibility : false

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let eye = !HTMLDivElement.prototype.isPrototypeOf(target) ? target.parentNode : target

            if (!visibility) {

                setTimeout((function () {

                    // noinspection JSPrimitiveTypeWrapperUsage
                    target.visibility = this.eyeVisibleOff(eye)

                }).bind(this), 6e2)

                // noinspection JSPrimitiveTypeWrapperUsage
                target.visibility = this.eyeVisible(eye)

            } else {

                // noinspection JSPrimitiveTypeWrapperUsage
                target.visibility = this.eyeVisibleOff(eye)
            }
        }
    }

    static eyeVisible(e) {

        let target = PointerEvent.prototype.isPrototypeOf(e) ? e && e.target : e

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let matIcon = target.querySelector("i.material-icons")

            if (matIcon && HTMLElement.prototype.isPrototypeOf(matIcon)) {

                matIcon.classList.remove("visibility_off")

                matIcon.classList.add("visibility")
                matIcon.classList.add("activate")
            }

            let passInput = target.parentNode.querySelector("input[type=\"password\"]")
            if (passInput && HTMLElement.prototype.isPrototypeOf(passInput))
                passInput.setAttribute("type", "text")
        }

        return true
    }

    static eyeVisibleOff(e) {

        let target = PointerEvent.prototype.isPrototypeOf(e) ? e && e.target : e

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let matIcon = target.querySelector("i.material-icons")

            if (matIcon && HTMLElement.prototype.isPrototypeOf(matIcon)) {

                matIcon.classList.remove("visibility")
                matIcon.classList.remove("activate")

                matIcon.classList.add("visibility_off")
            }

            let passInput = target.parentNode.querySelector("input[type=\"text\"]")
            if (passInput && HTMLElement.prototype.isPrototypeOf(passInput))
                passInput.setAttribute("type", "password")
        }

        return false
    }
}