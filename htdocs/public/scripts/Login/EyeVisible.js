// <div className="pass center row nowrap">
//     <input type="password"
//            name="password"
//            id="userPassword"
//            placeholder="password"
//            spellCheck="false"/>
//     <label htmlFor="userPassword"></label>
//     <div className="eye center">
//         <i className="material-icons md-24 md-dark visibility_off"></i>
//     </div>
// </div>

export default class EyeVisible {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory

    static Main() {

        this.EyeVisible()
    }

    static EyeVisible() {

        this.jessieQuery.styleInsertRule(".material-icons.visibility_off:before { color: var(--var-grey); }")
        this.jessieQuery.styleInsertRule(".material-icons.visibility.activate:before { color: var(--var-black); }")

        let passNodes = document.querySelectorAll("div.pass")

        if (passNodes.length > 0) {

            Array.from(passNodes).forEach((function (node) {

                let eye = node.querySelector("div.eye")
                eye.addEventListener("click", this.eyeOnClickListener.bind(this))
                eye.visibility = false
            }).bind(this))
        }
    }

    static eyeOnClickListener(e) {

        let target = e && "target" in e && e.target
        let visible = "visibility" in target ? target.visibility : false

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let eye = !HTMLDivElement.prototype.isPrototypeOf(target) ? target.parentNode : target

            if (!visible) {

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