

export default class Activity {

    static Main() {

        let eye = document.querySelector("i.bi.bi-eye-slash")
        eye.addEventListener("click", this.eyeClick)
    }

    static eyeClick(e) {

        console.log(e)
    }
}