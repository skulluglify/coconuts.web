/**
 * Create By Ahmad Asy SyafiQ
 * Load main Class Look like Java
 * I Hate My Code
 */
import JessieQuery from "./query.mjs";

/**
 * @param {Window} global
 */
(async function (global) {

    // rootDir
    let formats = [ "htm", "html", "xhtml", "php", "asp", "aspx", "pl", "plx" ]
    let pathname = location.pathname.split("\/")
    let suffix = pathname.pop()
    let prefix = pathname.join("\/")

    if (suffix.includes("\.")) {

        let format = suffix.split("\.").pop()
        if (!formats.includes(format)) {

            throw "[JessieCore] Not Supported!"
        }
    } else {

        // not have index file
        // maybe using route views
        prefix = prefix + "\/" + suffix
    }

    // get rootDir
    let rootDir = location.origin + prefix

    let pathClass = Array
        .from(document.querySelectorAll("script[src*='jessie/core.mjs'][data-main*='.Activity']"))
        /**
         * @param {Element} node
         */
        .map(function (node) {

            // path scripts
            if (node && HTMLElement.prototype.isPrototypeOf(node)) {

                let context = "main" in node.dataset ? node.dataset.main : null

                if (context && context.startsWith("\.")) {

                    let path = context && context.endsWith(".Activity") ?
                        rootDir + context.replace(/\./g, "\/") + "\.mjs" :
                        null

                    // set name, path into array map
                    context = context.startsWith("\.") ? context.split("\.").pop() : context
                    return new Map([
                        [ context, path ]
                    ])
                }
            }

            return [ null, null ]
        })

    let mainClass = await Promise.all(

        /**
         * @param {Array} o
         */
        pathClass.reduce(function (o, sourceMap) {

            // get name, path from array map
            let [ nameClass, sourceClass ] = Array.from(sourceMap.entries())[0]

            if (!!nameClass && !!sourceClass) {

                o.push((async function() {

                    // load module
                    let module = await import(sourceClass)
                    return [ nameClass, module ]
                })())
            }

            return o
        }, [])
    )

    function convertArrayToObject(array, o) {
        let obj = {}
        if (o && Object.prototype.isPrototypeOf(o)) obj = o
        if (array && Array.isArray(array))
            return array.reduce(function (o, a) {
                let [ key, value ] = a
                Object.defineProperty(o, key, {
                    value: value,
                    configurable: true,
                    enumerable: true,
                    writable: false
                })
                return o
            }, obj)
        return null
    }

    // query from url it self
    let query = location?.search
    if (query && query.startsWith("\?")) query = query.substring(1)
    query = query && query
        .split("&")
        .map(function (vars) {
            let [ key, context] = vars.split("=")
            let value = context && context.includes("\%") ? decodeURIComponent(context) : context
            return [ key, value ]
        })

    function ready() {

        if (document.readyState === "complete") {

            /**
             * @param {Awaited<Array>} link
             */
            let main = mainClass.map(function (link) {

                let callback = null
                let [ name, program ] = link
                let main = program.default
                if (typeof main == "function") {
                    if (main.name === name) {
                        if ("Main" in main) {
                            if (typeof main.Main == "function") {

                                convertArrayToObject(query, main)

                                // Embedded JessieQuery in Class<Object>
                                Object.defineProperty(main, "jessieQuery", {
                                    value: new JessieQuery(main), // Auto Call
                                    configurable: true,
                                    enumerable: false,
                                    writable: false
                                })

                                // Binding main in Main (Self)
                                callback = main.Main.bind(main)
                            }
                        }
                    } else {

                        throw`MainClass ${name} Not Same As File Name!`
                    }
                }

                return callback
            })

            // execute all main
            let taskl = main.reduce(function (o, e) {

                if (e && typeof e == "function")
                    o.push((function Anonymous() {

                        // Isolate, Wrapping, No More
                        return !e() ? Anonymous : e
                    })())

                return o
            }, [])

            // global.taskl = taskl

            ready = null // break point
        }
    }

    // HEAD: initialize
    global.document.addEventListener("load", ready)
    global.addEventListener("DOMContentLoaded", ready)
    global.addEventListener("load", ready)

    // BODY: initialize
    ready(null)

})(window, {})