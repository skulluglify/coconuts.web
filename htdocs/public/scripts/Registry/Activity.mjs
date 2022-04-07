import EyeVisible from "../Login/EyeVisible.mjs";
import CheckBox from "./CheckBox.mjs";
import DateOption from "./DateOption.mjs";
import Popover from "./Popover.mjs";

export default class Activity {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory
    static DateOption // DateOption Module Bindings Allocated Memory
    static Popover // Popover Module Bindings Allocated Memory

    static Main() {

        // Embedded Module Into Self Class
        this.jessieQuery.Module.Extends(

            EyeVisible,
            CheckBox,
            DateOption,
            Popover
        )

        document.querySelector("div.date-option")
            .addEventListener("click", (function () {

                console.log(this.DateOption.getDateValue())
            }).bind(this))

        console.log(this.DateOption.getDateValue())

        this.Popover.setPopContent("Date of Your Birthday!")
    }
}