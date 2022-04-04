import EyeVisible from "./EyeVisible.mjs";

export default class Activity extends EyeVisible {

    static jessieQuery;

    constructor() {

        super()
    }

    static Main() {

        let regisBtn = document.querySelector("button#registry")

        if (!!regisBtn)
            regisBtn.addEventListener("click", function (e) {

                open("registry.html", false)
            })

        this.InitEyeVisible()
    }
}