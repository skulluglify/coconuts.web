import EyeVisible from "../Login/EyeVisible.mjs";
import CheckBoxAuto from "./CheckBoxAuto.js";

export default class Activity {

    static jessieQuery;

    static Main() {

        this.jessieQuery.loadModule(EyeVisible)
        this.jessieQuery.loadModule(CheckBoxAuto)
    }
}