export default class Activity {

    static jessieQuery

    static Main() {

        let desc = document.querySelector("div.content div.user-description")

        let data = {

            "user_name": "Ahmad Asy SyafiQ",
            "user_dob": "07-07-2002",
            "user_gender": "male"
        }

        for (let key in data) {

            desc.appendChild(this.spanText(data[key]))
        }
    }

    static spanText(context) {

        if (context && typeof context == "string" && context.length > 0) {

            let span = document.createElement("span")
            span.textContent = context
            return span
        }

        return null
    }
}