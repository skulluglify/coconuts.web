import EyeVisible from "./EyeVisible.mjs";

export default class Activity {

    static jessieQuery;

    static Main() {

        let regisBtn = document.querySelector("button#registry")

        if (!!regisBtn)
            regisBtn.addEventListener("click", function (e) {

                open("registry.html")
            })

        this.jessieQuery.loadModule(EyeVisible)
    }
}