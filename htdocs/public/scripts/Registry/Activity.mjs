import EyeVisible from "../Login/EyeVisible.mjs";
import CheckAuto from "./CheckAuto.js";

export default class Activity {

    static jessieQuery;

    static Main() {

        this.jessieQuery.loadModule(EyeVisible)
        this.jessieQuery.loadModule(CheckAuto)
    }
}