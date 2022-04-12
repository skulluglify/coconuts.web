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

        this.PopUp()
    }

    static PopUp() {

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

            // info success | warning | dangerous
            let infoClass

            switch (info) {

                case "none":

                    infoClass = "pop-none"
                    break;

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

                let span, message
                let info = target.querySelector("div.info")
                let content = target.querySelector("div.content")

                if (info && content) {

                    // Remove Class
                    for (let cls of info.classList)
                        if (cls.startsWith("pop\-"))
                            info.classList.remove(cls)
                        else if (cls.startsWith("animate\_\_"))
                            info.classList.remove(cls)

                    for (let cls of content.classList)
                        if (cls.startsWith("animate\_\_"))
                            content.classList.remove(cls)

                    if (!(Array.from(content.classList).includes("animate__animated")))
                        content.classList.add("animate__animated", "animate__bounceIn")

                    if (!(Array.from(info.classList).includes("animate__animated")))
                        info.classList.add(infoClass, "animate__animated", "animate__flipInY")

                    message = content.querySelector("div.message")
                    if (message) {

                        if (context) {

                            if (typeof context == "string" && context.length > 0) {

                                span = message.querySelector("span")
                                if (!span) {

                                    // Create Span
                                    span = document.createElement("span")
                                    message.appendChild(span)
                                }

                                span.textContent = context
                            } else if (HTMLElement.prototype.isPrototypeOf(context)) {

                                message.appendChild(context)
                            }
                        }
                    }
                }

                let prompt = target.querySelector("div.prompt")
                if (prompt) {

                    let submit, cancel

                    switch (type) {

                        case "prompt":

                            cancel = this.createButton("cancel")
                            submit = this.createButton("submit")
                            break;
                        case "submit":

                            submit = this.createButton("submit")
                            break;
                        default:

                            submit = this.createButton("submit")
                            break;
                    }

                    let contentListener = function __Listener__() {

                        if ("types" in content.dataset)
                            if (["failure", "success"].includes(content.dataset.types)) {

                                for (let cls of info.classList)
                                    if (cls.startsWith("pop\-"))
                                        info.classList.remove(cls)
                                    else if (cls.startsWith("animate\_\_"))
                                        info.classList.remove(cls)

                                for (let cls of content.classList)
                                    if (cls.startsWith("animate\_\_"))
                                        content.classList.remove(cls)

                                target.style.display = "none"

                                if (cancel) cancel.remove()
                                if (submit) submit.remove()
                                if (span) span.remove()
                                if (message)
                                    for (let node of message.children)
                                        node.remove() // remove from message, but current element not deleted

                                if (callback && typeof callback == "function") {

                                    // Return Event
                                    let events = content.dataset.types // content.dataset.types
                                    content.dataset.types = ""
                                    content.removeEventListener("animationend", __Listener__)
                                    let event = new Event(events)
                                    if (contentListener) contentListener = null
                                    callback(event)
                                }
                            }

                        // Make it Late (Synchronous)
                        // setTimeout(function () {

                            // enable clicked
                            if (cancel) cancel.disabled = false
                            if (submit) submit.disabled = false

                        // }, 1e2)
                    }

                    content.addEventListener("animationend", contentListener) /*options: { once: true }*/

                    let promptEventFn = (types) => () => {

                        for (let cls of content.classList)
                            if (cls.startsWith("animate\_\_"))
                                content.classList.remove(cls)

                        content.dataset.types = types
                        content.classList.add("animate__animated", "animate__bounceOut")
                    }

                    if (cancel) {

                        // disable clicked
                        cancel.disabled = true
                        prompt.appendChild(cancel)
                        cancel.addEventListener("click", promptEventFn("failure"), { once: true })
                    }

                    if (submit) {

                        // disable clicked
                        submit.disabled = true
                        prompt.appendChild(submit)
                        submit.addEventListener("click", promptEventFn("success"), { once: true })
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
                case "cancel":

                    btn.classList.add("cancel", "danger")
                    btn.textContent = "cancel"
                    break;
            }

            return btn
        }

        return null
    }
}