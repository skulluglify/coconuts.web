/**
 * Alert
 * SendRequest
 * Web Worker
 * Email Validator
 * Session Manager
 * Cookie*/

import PopUp from "./PopUp.js";

export default class Utilities {

    static jessieQuery
    static PopUp

    static Main() {

        this.jessieQuery.Module.Extends(

            PopUp
        )
    }

    static Utilities() {}

    static Collections() {

        return [

            this.isEmail,
            this.Alert,
            this.SendRequest,
            this.cloneElement,
            this.convertDataURLToBlob,
            this.getFormatFileFromDataURl,
            this.getDataFileFromDataURl
        ]
    }

    /**
     * @param {string,null} context
     * @return {boolean, null}*/
    static isEmail(context) {

        if (context && typeof context == "string" && context.length > 0) {

            let emailValidator = new RegExp(/^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i)
            return emailValidator.test(context)
        }

        return null
    }

    /**
     * @param {string} info
     * @param {string} message
     * @param {string} type
     * @return {Promise}*/
    static async Alert(info, message, type= "submit") {

        return new Promise((function (resolve, reject) {

            try {

                this.PopUp.createPopUp({

                    info: info,
                    message: message,
                    type: type,
                    callback: resolve
                })

            } catch (e) {

                reject(e)
            }

        }).bind(this))
    }

    /**
     * @param {Object} data
     * @return {Promise<Awaited<Response>[]>,Promise<unknown>} data
     * */
    static async SendRequest(data) {

        if (data && typeof data == "object" && !Array.isArray(data)) {

            let requests = Object.keys(data).map(function (key) {

                let bodyMap = {}
                let options = {}
                let url = [ location.origin, "public/v1", key ].join("\/")

                // Make it Single Request
                let content = data[key]
                bodyMap[key] = content

                options.headers = {
                    "Content-Type": "application/json"
                }

                if (content && content instanceof FormData)
                    delete options.headers

                return fetch(url, Object.assign({

                    method: "POST",
                    mode: "cors",
                    cache: "no-cache",
                    credentials: "same-origin",
                    redirect: "follow",
                    referrerPolicy: "origin",
                    body: content && content instanceof FormData ? content : JSON.stringify(bodyMap)
                }, options))
            })

            return Promise.all(requests)
        }

        return new Promise(function (resolve, reject) {

            // Promise Like Promise
            reject(null)
        })
    }

    /**
     * @param {Element, null} target
     * @return {Node, null}
     */
    static cloneElement(target) {

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let parent = target.cloneNode()
            if (parent) {

                if (target.children.length > 0) {

                    for (let children of target.children)
                        if (children) {

                            let child = this.cloneElement(children)
                            if (child) parent.appendChild(child)
                        }

                } else {

                    let context = target.textContent
                    if (context && typeof context == "string" && context.length > 0)
                        parent.textContent = context
                }

                return parent
            }
        }

        return null
    }

    /**
     * @param {string,null} context
     * @return {Blob, null}*/
    static convertDataURLToBlob(context) {

        if (context && typeof context == "string" && context.length > 0) {

            if (context.startsWith("data:") && context.includes(";base64,")) {

                let mimeType = this.getFormatFileFromDataURl(context)
                let data = atob(this.getDataFileFromDataURl(context))
                let buffer = new Uint8Array(Array.from(data).map(c => c.charCodeAt(0)))

                if (buffer.length > 0)
                return new Blob([
                    buffer
                ], {
                    type: mimeType
                })
            }
        }

        return null
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

    static getDataFileFromDataURl(context) {

        if (context && typeof context === "string" && context.length > 0) {

            let temp, start
            temp = ""
            start = false

            for (let c of context) {

                if (start) temp += c
                if (c === ",") start = true
            }

            return temp
        }

        return null
    }
}