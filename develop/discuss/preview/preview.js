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
    
            let w = this.attributes?.width?.value || "100%"
            let h = this.attributes?.height?.value || "auto"
            let z = this.attributes?.class?.value || ""
            let s = this.attributes?.src?.value || "preview/index.html"
            let b = this.attributes?.frameborder?.value || "0"
            let c = this.attributes?.crossorigin?.value || "anonymous"

            let d = this.attributes?.content?.value || ""
    
            if (d.length > 0) {
                s = s + "?content=" + encodeURIComponent(d)
            }
    
            let iframe = document.createElement("iframe")
            iframe.width = w
            iframe.height = h
            iframe.src = s
            iframe.frameborder = b
            iframe.crossorigin = c
            iframe.style.textDecoration = "none"
            iframe.style.border = "none"
            iframe.style.margin = "0"
            iframe.style.padding = "0"
            iframe.style.overflow = "hidden"
            iframe.addEventListener("load", (e) => {
                
                let t = e && e.target
                if (!!t) {

                    let d = t.contentWindow && t.contentWindow.document
                    if (!!d) {
                    
                        let h = (d.body && d.body.scrollHeight) || 0
                        if(h > 0) {
                            t.style.height = h + 52 + "px"
                        }
                        if (!!b && !!z) d.body.classList.add(z.split(' '))
                    }
                }
            })
            this.shadow.append(iframe)
        }
    }
    let p = !1
    let f = (e) => {
        if (document.readyState === "complete" && !p) {
            customElements.define("w-frame", HTMLWFrameElement);
            p = !0
        }
    }
    window.addEventListener("DOMContentLoaded", f)
    document.addEventListener("load", f)
    window.addEventListener("load", f)
    f(null)
}).call(globalThis, globalThis)