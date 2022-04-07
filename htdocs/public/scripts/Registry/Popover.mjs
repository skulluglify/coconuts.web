import Rect, {Cartesian} from "../../jessie/rect.mjs";

// <button className="popover" autoFocus>
//     <div className="content">
//         <span>Hello, World!</span>
//     </div>
//     <div className="arrow"></div>
// </button>

export default class Popover {

    // {Element} pop
    static pop
    static jessieQuery

    static Main() {

        this.InitPopover()
    }

    static InitPopover() {

        // Styling Popover
        this.jessieQuery.styleInsertRule("button.popover\n" +
            "{\n" +
            "\n" +
            "    display: none;\n" +
            "    position: absolute;\n" +
            "    top: 0;\n" +
            "    right: auto;\n" +
            "    bottom: auto;\n" +
            "    left: 0;\n" +
            "    width: fit-content;\n" +
            "    height: fit-content;\n" +
            "    margin: 0;\n" +
            "    padding: 10px;\n" +
            "    color: var(--var-black);\n" +
            "    border: 1px solid var(--var-grey);\n" +
            "    border-radius: 0;\n" +
            "    background-color: var(--var-white);\n" +
            "    overflow: visible;\n" +
            "}")

        this.jessieQuery.styleInsertRule("button.popover > div.content\n" +
            "{\n" +
            "\n" +
            "    width: 100%;\n" +
            "    height: 100%;\n" +
            "}")

        this.jessieQuery.styleInsertRule("button.popover > div.content > span\n" +
            "{\n" +
            "\n" +
            "    font-size: 1em;\n" +
            "}")

        this.jessieQuery.styleInsertRule("button.popover > div.arrow\n" +
            "{\n" +
            "    content: \"\";\n" +
            "    position: absolute;\n" +
            "    width: 0;\n" +
            "    height: 0;\n" +
            "    padding: 0;\n" +
            "    margin: 0 0 -12px 0;\n" +
            "    border-top: 12px solid var(--var-grey);\n" +
            "    border-right: 8px solid transparent;\n" +
            "    border-bottom: 0;\n" +
            "    border-left: 8px solid transparent;\n" +
            "    border-radius: 0;\n" +
            "}")

        // Initialize
        let popover = this.GetPopover()
        let target = document.querySelector("div.user-dob")

        if (popover && target && Array.isArray(popover) && HTMLElement.prototype.isPrototypeOf(target)) {

            let [ pop, popRect ] = popover

            console.log(popRect.toString())

            let targetRect = Rect.getElementRect(target)

            this.setPopArrowPosition(pop, 2)

            // Auto Adjustable by Window Resize, Scrolling
            Array.from(["resize", "scroll"]).map((function (events) {

                window.addEventListener(events, (function (e) {

                    let popRect = Rect.getElementRect(pop)
                    let targetRect = Rect.getElementRect(target)

                    if (popRect && targetRect) {

                        let rect = this.getPopTopStartRect(popRect, targetRect)

                        if (rect.Y < scrollY) {

                            rect = this.getPopBottomStartRect(popRect, targetRect)
                            this.setPopArrowPosition(pop, 0)

                        } else {

                            this.setPopArrowPosition(pop, 2)
                        }

                        this.setPopoverPosition(pop, rect)
                    }

                }).bind(this))

            }).bind(this))

            this.setPopoverPosition(pop, this.getPopTopStartRect(popRect, targetRect))

            // this.setContent(pop, "Date of birthday !")
            this.pop = pop // set new allocated memory
        }
    }

    static setPopContent(content) {

        if (this.pop) {

            this.setContent(this.pop, content)
        }
    }

    static Collections() {

        return [

            this.setPopContent
        ]
    }

    static GetPopover() {

        let target = document.querySelector("button.popover")

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            target.style.display = "block"

            let rect = Rect.getElementRect(target)

            target.style.display = "none"

            target.addEventListener("focusout", function () {

                target.style.display = "none"
            })

            return [ target, rect ]

        }

        return null
    }

    /**
     * 0 top
     * 1 right
     * 2 bottom
     * 3 left
     * */
    static setPopArrowPosition(parent, pos) {

        if (parent && HTMLElement.prototype.isPrototypeOf(parent)) {

            let target = parent.querySelector("div.arrow")

            if (target && HTMLElement.prototype.isPrototypeOf(target)) {

                // /* Default bottom */
                // margin: 0 0 -12px 0;
                // border-top: 12px solid var(--var-grey);
                // border-right: 8px solid transparent;
                // border-bottom: 0;
                // border-left: 8px solid transparent;

                // from top, right, bottom, left
                // can up to
                /*
                + = = = + = = = +
                |       |       |
                + = = = + = = = +
                |       |       |
                + = = = + = = = +
                 */
                // topStart topCenter topEnd
                // endTop endCenter endBottom
                // bottomStart bottomCenter bottomEnd
                // startTop startCenter startBottom
                // explicit
                // centerCenter

                switch (pos) {

                    case 0: // top

                        Object.assign(target.style, {

                            // topStart
                            top: "0",
                            right: "auto",
                            bottom: "auto",
                            left: "0",

                            marginTop: "-12px",
                            marginRight: "0",
                            marginBottom: "0",
                            marginLeft: "0",
                            borderTop: "0",
                            borderRight: "8px solid transparent",
                            borderBottom: "12px solid var(--var-grey)",
                            borderLeft: "8px solid transparent"
                        })

                        break;
                    case 1: // right

                        Object.assign(target.style, {

                            // endTop
                            top: "0",
                            right: "0",
                            bottom: "auto",
                            left: "auto",

                            marginTop: "0",
                            marginRight: "-12px",
                            marginBottom: "0",
                            marginLeft: "0",
                            borderTop: "8px solid transparent",
                            borderRight: "0",
                            borderBottom: "8px solid transparent",
                            borderLeft: "12px solid var(--var-grey)"
                        })

                        break;
                    case 2: // bottom

                        Object.assign(target.style, {

                            // bottomStart
                            top: "auto",
                            right: "auto",
                            bottom: "0",
                            left: "0",

                            marginTop: "0",
                            marginRight: "0",
                            marginBottom: "-12px",
                            marginLeft: "0",
                            borderTop: "12px solid var(--var-grey)",
                            borderRight: "8px solid transparent",
                            borderBottom: "0",
                            borderLeft: "8px solid transparent"
                        })

                        break;
                    case 3: // left

                        Object.assign(target.style, {

                            // startTop
                            top: "0",
                            right: "auto",
                            bottom: "auto",
                            left: "0",

                            marginTop: "0",
                            marginRight: "0",
                            marginBottom: "0",
                            marginLeft: "-12px",
                            borderTop: "8px solid transparent",
                            borderRight: "12px solid var(--var-grey)",
                            borderBottom: "8px solid transparent",
                            borderLeft: "0"
                        })

                        break;
                    default: // unknown

                        break;
                }
            }
        }
    }

    static getPopTopStartRect(popRect, targetRect) {

        if (popRect && targetRect && Rect.prototype.isPrototypeOf(popRect) && Rect.prototype.isPrototypeOf(targetRect)) {

            let popBottomStart = popRect.Corner.BottomStart
            let targetTopStart = targetRect.Corner.TopStart

            let targetX = targetTopStart.X
            let targetY = targetTopStart.Y

            let popX = popBottomStart.X
            let popY = popBottomStart.Y

            let x = targetX - popX
            let y = targetY - popY - 4 // Arrow Size

            return new Cartesian(x, y)
        }

        return new Cartesian(0, 0)
    }

    static getPopBottomStartRect(popRect, targetRect) {

        if (popRect && targetRect && Rect.prototype.isPrototypeOf(popRect) && Rect.prototype.isPrototypeOf(targetRect)) {

            let popTopStart = popRect.Corner.TopStart
            let targetBottomStart = targetRect.Corner.BottomStart

            let targetX = targetBottomStart.X
            let targetY = targetBottomStart.Y

            let popX = popTopStart.X
            let popY = popTopStart.Y

            let x = targetX - popX
            let y = targetY - popY + 4 // Arrow T 1/3

            return new Cartesian(x, y)
        }

        return new Cartesian(0, 0)
    }

    static setPopoverPosition(target, cartesian) {

        if (target && cartesian && HTMLElement.prototype.isPrototypeOf(target) && Cartesian.prototype.isPrototypeOf(cartesian)) {

            target.style.transform = "translate(" + cartesian.X + "px, " + cartesian.Y + "px )"
            target.style.display = "block"

            // set Auto focus
            if (HTMLButtonElement.prototype.isPrototypeOf(target))
                target.focus()
        }
    }

    static setContent(target, context) {

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let content = target.querySelector("div.content")
            if (context && typeof context == "string") {

                let span = content.querySelector("span")
                if (span && HTMLElement.prototype.isPrototypeOf(span)) {

                    span.textContent = context
                }

            } else
            if (context && HTMLElement.prototype.isPrototypeOf(context)) {

                // Maybe First Using :P
                // if (content.children.length > 0)
                    // for (let el of content.children)
                        // el.remove()

                content.appendChild(context)
            }
        }
    }
}
