import EyeVisible from "../Login/EyeVisible.mjs";
import CheckBox from "./CheckBox.mjs";
import DateOption from "./DateOption.mjs";
import PopOver from "./PopOver.mjs";
import PopUp from "./PopUp.mjs";

export default class Activity {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory
    static DateOption // DateOption Module Bindings Allocated Memory
    static PopOver // PopOver Module Bindings Allocated Memory
    static PopUp // PopUp Module Bindings Allocated Memory

    static Main() {

        // Embedded Module Into Self Class
        this.jessieQuery.Module.Extends(

            EyeVisible,
            CheckBox,
            DateOption,
            PopOver,
            PopUp
        )

        document.querySelector("button#registry")
            .addEventListener("click", (function () {

                let date = new Date()
                let userDate = new Date(this.DateOption.getDateValue())
                let userAge = date.getFullYear() - userDate.getFullYear()

                if (userAge > 18) {

                    this.PopUp.createPopUp({

                        info: "success",
                        message: "Your Date " + this.DateOption.getDateValue(),
                        type: "prompt",

                        callback: (e) => {

                            if (e.type == "success") {

                                this.PopUp.createPopUp({

                                    info: "success",
                                    message: "How are you today!",
                                    type: "submit"
                                })
                            }
                        }
                    })
                } else {

                    this.PopUp.createPopUp({

                        info: "dangerous",
                        message: "Restrict age below 18 years!",
                        type: "submit"
                    })
                }
            }).bind(this))

        console.log(this.DateOption.getDateValue())

        this.PopOver.setPopContent("Date of Your Birthday!")
    }
}