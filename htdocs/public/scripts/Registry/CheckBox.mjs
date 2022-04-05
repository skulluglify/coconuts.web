// <div className="radio user-gender start row">
//     <div className="check start row nowrap male">
//         <input type="radio" name="gender" id="genderMale" checked/>
//         <label htmlFor="genderMale">male</label>
//     </div>
//     <div className="check start row nowrap female">
//         <input type="radio" name="gender" id="genderFemale"/>
//         <label htmlFor="genderFemale">female</label>
//     </div>
// </div>

export default class CheckBox {

    static jessieQuery; // JessieQuery Module Bindings Allocated Memory

    static Main() {

        this.InitCheckBox()
    }

    static InitCheckBox() {

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