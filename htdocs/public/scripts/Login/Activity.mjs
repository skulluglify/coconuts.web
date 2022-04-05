import EyeVisible from "./EyeVisible.mjs";

export default class Activity {

    static jessieQuery; // JessieQuery Module Bindings Allocated Memory

    static Main() {

        // Embedded Module Into Self Class
        this.jessieQuery.Module.Extends(

            EyeVisible
        )

        let regisBtn = document.querySelector("button#registry")
        if (!!regisBtn) {
            /**
             * @param {Event} e
             */
            regisBtn.addEventListener("click", function (e) {

                open("registry.html")
            })

        }
    }
}