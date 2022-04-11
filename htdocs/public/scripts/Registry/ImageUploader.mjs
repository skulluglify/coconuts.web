// <div className="user-photo">
//     <div className="image"></div>
// </div>

import Utilities from "./Utilities.mjs";

export default class ImageUploader {

    static jessieQuery // JessieQuery Module Bindings Allocated Memory
    static Utilities // JessieQuery Module Bindings Allocated Memory
    static target // create new Allocated Memory
    static dataURL // create new Allocated Memory
    static file // create new Allocated Memory

    static Main() {

        this.jessieQuery.Module.Extends(
            Utilities
        )
        // this.InitImagePreview()
        // console.log("Main")
    }

    /**
     * @param {Element, null} target*/
    static InitImageUploader(target = null) {

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            this.target = target

            // let fileUpload = target.querySelector("input[type='file']#upload")
            let fileUpload = target.querySelector("label[for='upload']")
            let canvasPreview = target.querySelector("canvas#preview")

            if (fileUpload && canvasPreview) {

                let fileListener = (function __Listener__(e) {

                    e.preventDefault()
                    e.stopPropagation()

                    // console.log(e.dataTransfer.items)

                    let target = e && "target" in e && e.target

                    if (target) {

                        let stream = new FileReader

                        if ("dataTransfer" in e) {
                            // DataTransferItem
                            if ("items" in e.dataTransfer && e.dataTransfer.items.length > 0) {

                                let transfer = e.dataTransfer.items[e.dataTransfer.items.length - 1]
                                let file = transfer.getAsFile()

                                if (file && file instanceof File) {
                                    stream.readAsDataURL(file)
                                    this.file = file
                                }
                            }
                        }

                        if ("files" in target && target.files.length > 0) {

                            let file = target.files[target.files.length - 1]

                            if (file instanceof File) {
                                stream.readAsDataURL(file)
                                this.file = file
                            }
                        }

                        stream.addEventListener("load", (function (o) {

                            let target = o && "target" in o && o.target

                            if (target) {


                                let formatFile = this.getFormatFileFromDataURl(target.result)
                                if (["image/png", "image/svg+xml", "image/jpeg"].includes(formatFile)) {

                                    let img = document.createElement("img")

                                    let width = canvasPreview.getAttribute("width") || "180px"
                                    let height = canvasPreview.getAttribute("height") || "180px"

                                    img.setAttribute("src", target.result)
                                    img.setAttribute("width", width)
                                    img.setAttribute("height", height)

                                    let context = canvasPreview.getContext("2d")

                                    if (context && CanvasRenderingContext2D.prototype.isPrototypeOf(context)) {

                                        context.clearRect(0, 0, 180, 180)

                                        // fill background as white
                                        context.fillStyle = "#DEDEDE"
                                        context.fillRect(0, 0, 180, 180)

                                        img.addEventListener("load", (function (t) {

                                            let target = t && "target" in t && t.target
                                            if (target) {

                                                context.drawImage(img, 0, 0, 180, 180)

                                                this.dataURL = canvasPreview.toDataURL(formatFile, 1.0)
                                                // console.log(canvasPreview.toDataURL(formatFile, 1.0).length)
                                                // console.log(img.src.length)
                                                // context.clearRect(0, 0, 256, 256)
                                            }
                                        }).bind(this))
                                    }
                                }
                            }

                        }).bind(this))
                    }

                }).bind(this)

                // click and drop
                for (let events of ["change", "drop", "drag", "dragenter", "dragover", "dragleave"])
                    fileUpload.addEventListener(events, fileListener)
            }

            // Displayed
            if ("style" in target)
                Object.assign(target.style, {

                display: "flex"
            })
        }
    }

    static async show() {

        let target = this.target
        if (target && HTMLElement.prototype.isPrototypeOf(target))
            await this.Utilities.Alert("none", target)
    }

    /**
     * @return {string, null}
     * */
    static getAsDataURL() {

        return this.dataURL || null
    }

    /**
     * @return {File, null}
     * */
    static getAsFile() {

        return this.file || null
    }

    static Collections() {

        return [

            this.show,
            this.getAsDataURL,
            this.getAsFile
        ]
    }

    static getFormatFileFromDataURl(context) {

        if (context && typeof context === "string" && context.length > 0) {

            let temp, start
            temp = ""
            start = false

            for (let c of context) {

                if (c === ";") break
                if (start) temp += c
                if (c === ":") start = true
            }

            return temp
        }

        return null
    }
}