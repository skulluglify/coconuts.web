// <div className="popup">
//     <div className="content">
//         <div className="info pop pop-info"></div>
//         <div className="message">
//             <span>Hello, World!</span>
//         </div>
//         <div className="prompt">
//             <button className="submit larger primary">submit</button>
//         </div>
//     </div>
// </div>

export default class PopUp {

    static jessieQuery

    static Main() {

        this.InitPopUp()
    }

    static InitPopUp() {

        // info success | warning | dangerous
        // message text
        // type prompt | submit
        // callback

        // this.createPopUp({
        //
        //     info: "success",
        //     message: "Hello, World!",
        //     type: "prompt"
        // })
    }

    static Collections() {

        return [

            this.createPopUp
        ]
    }

    static createPopUp(options) {

        if (options && options instanceof Object) {

            let info = "info" in options ? options.info : "unknown"
            let context = "message" in options ? options.message : null
            let type = "type" in options ? options.type : "submit"
            let callback = "callback" in options ? options.callback : null

            let target = document.querySelector("div.popup")
            let popClassList = [ "pop-success", "pop-warn", "pop-info", "pop-danger", "pop-unknown" ]

            // info success | warning | dangerous
            let infoClass

            switch (info) {

                case "info":

                    infoClass = "pop-info"
                    break;
                case "success":

                    infoClass = "pop-success"
                    break;
                case "warning":

                    infoClass = "pop-warn"
                    break;
                case "dangerous":

                    infoClass = "pop-danger"
                    break;
                case "unknown":

                    infoClass = "pop-unknown"
                    break;
                default:

                    infoClass = "pop-unknown"
                    break;
            }

            if (target) {

                target.style.display = "flex"

                let info = target.querySelector("div.info")
                let content = target.querySelector("div.content")

                if (info && content) {

                    // Remove Class
                    info.classList.remove(...popClassList)
                    info.classList.add(infoClass)

                    let message = content.querySelector("div.message")
                    if (message) {

                        let span = message.querySelector("span")
                        if (!span) {

                            // Create Span
                            span = document.createElement("span")
                            message.appendChild(span)
                        }

                        if (context && typeof context == "string" && context.length > 0) {

                            span.textContent = context
                        }
                    }
                }

                let prompt = target.querySelector("div.prompt")
                if (prompt) {

                    let submit, cencel

                    switch (type) {

                        case "prompt":

                            cencel = this.createButton("cencel")
                            submit = this.createButton("submit")
                            break;
                        case "submit":

                            submit = this.createButton("submit")
                            break;
                        default:

                            submit = this.createButton("submit")
                            break;
                    }

                    if (cencel) {

                        prompt.appendChild(cencel)
                        cencel.addEventListener("click", function () {

                            target.style.display = "none"
                            if (submit) submit.remove()
                            cencel.remove()

                            if (callback && typeof callback == "function") {

                                // Return Event
                                let event = new Event("failure")
                                callback(event)
                            }
                        })
                    }
                    if (submit) {

                        prompt.appendChild(submit)
                        submit.addEventListener("click", function () {

                            target.style.display = "none"
                            if (cencel) cencel.remove()
                            submit.remove()

                            if (callback && typeof callback == "function") {

                                // Return Event
                                let event = new Event("success")
                                callback(event)
                            }
                        })
                    }
                }
            }
        }
    }

    static createButton(types) {

        let btn = document.createElement("button")
        if (btn) {

            switch (types) {
                case "submit":

                    btn.classList.add("submit", "primary")
                    btn.textContent = "Submit"
                    break;
                case "cencel":

                    btn.classList.add("cencel", "danger")
                    btn.textContent = "Cencel"
                    break;
            }

            return btn
        }

        return null
    }
}