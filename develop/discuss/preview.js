((global) => {
    class HTMLWFrameElement extends HTMLElement {

        constructor() {

            super()
            this.shadow = this.attachShadow({ mode: "open" })

        }

        connectedCallback() {

            this.render()
        }

        render() {

            let width, height, cls, frameborder, source, crossorigin, content, iframe, link, styleOptions
            width = this.getAttribute("width") || "100%"
            height = this.getAttribute("height") || "auto"
            cls = this.getAttribute("class") || ""
            source = this.getAttribute("src") || "about:blank"
            frameborder = this.getAttribute("frameborder") || "0"
            crossorigin = this.getAttribute("crossorigin") || "anonymous"
            content = this.getAttribute("content")

            iframe = document.createElement("iframe")
            link = document.createElement("link")

            iframe.setAttribute("width", width)
            iframe.setAttribute("height", height)
            iframe.setAttribute("src", source)
            iframe.setAttribute("frameborder", frameborder)
            iframe.setAttribute("crossorigin", crossorigin)

            link.setAttribute("rel", "stylesheet")
            link.setAttribute("type", "text/css")
            link.setAttribute("href", "../modules/style.css")

            // styling
            styleOptions = {
                textDecoration: "none",
                border: "none",
                margin: "0",
                padding: "0",
                overflow: "hidden"
            }

            Object.assign(iframe.style, styleOptions)

            let els = this.parseNoScript(content)

            cls = cls.trim()

            iframe.addEventListener("load", (e) => {

                let t = e && e.target
                if (!!t) {

                    let w = t && t.contentWindow
                    let doc = w && w.document

                    if (!!doc) {

                        // tricky

                        !0 && (async function main() {

                            // create styling
                            let style = doc.createElement("style")

                            // append first
                            doc.head.append(link)
                            doc.head.append(style)
                            
                            // collect then
                            let sheet = style.sheet

                            sheet.insertRule("* { margin: 0; padding: 0; outline: 0; font-family: 'Open Sans', 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; }")


                            if (!!els) {

                                for (let el of els) doc.body.append(el)
                            }
    
                            if (!!cls) doc.body.classList.add(cls.split(' ').filter(c => !!c)) // no empty string

                            // hidden scrollbar
                            doc.body.style.overflow = 'hidden'
                            t.style.overflow = 'hidden'
        
                            // collect header elements
                            let childs = Array.from(doc.head.children).map(el => {
        
                                return new Promise((resolve, reject) => {
        
                                    try {
        
                                        el.addEventListener("load", (e) => {
        
                                            resolve({
        
                                                width: Math.max(

                                                    doc.documentElement.clientWidth,
                                                    doc.documentElement.offsetWidth,
                                                    doc.documentElement.scrollWidth
                                                ),
        
                                                height: Math.max(

                                                    doc.documentElement.clientHeight,
                                                    doc.documentElement.offsetHeight,
                                                    doc.documentElement.scrollHeight
                                                )
                                            })
                                        })
        
                                    } catch (e) {
        
                                        reject(e)
                                    }
                                })
                            })

                            let width = 0, height = 0

                            let l = await Promise.all(childs)

                            for (let o of l) {

                                if (o.width > width) width = o.width
                                if (o.height > height) height = o.height

                            }


                            // iframe, fit content
                            // t.style.width = width + "px"
                            t.style.width = "100%"
                            t.style.height = height + "px"

                        })()
                    }
                }
            })

            this.shadow.append(iframe)
        }

        noEventJS(el) {

            if (el instanceof Element) {

                for (let key of el.attributes) {

                    let k = key?.name || key

                    if (typeof k == "string") {

                        if (k.startsWith("on")) el.removeAttribute(k)
                    }
                }

                for (let cel of el.children) this.noEventJS(cel)
            }
        }

        parseNoScript(context) {

            if (typeof context == "string") {

                let p = new DOMParser()
                let d = p.parseFromString(context, "text/html")


                if (d instanceof Document) {

                    for (let el of d.querySelectorAll("script")) el.remove()

                    this.noEventJS(d.body)

                    let nodes = d.body && d.body.children

                    if (!!nodes) return Array.from(nodes)
                }
            }

            return null
        }
    }

    // once
    let p = !1
    let f = (e) => {

        if (document.readyState === "complete" && !p) {
            customElements.define("w-frame", HTMLWFrameElement);
            p = !0
        }
    }

    // if ready yet
    window.addEventListener("DOMContentLoaded", f)
    document.addEventListener("load", f)
    window.addEventListener("load", f)
    f(null)

}).call(globalThis, globalThis)