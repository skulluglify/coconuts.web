// Create By Ahmad Asy SyafiQ
// Load main Class Look like Java

(async function (global) {

    let pathClass = Array
        .from(document.querySelectorAll("script[src*='jessie/main.mjs'][main*='.Activity']"))
        .map(function (node) {

            let context = node.getAttribute("main", null)
            let path = context && context.endsWith(".Activity") ?
                ".." + context.replace(/\./g, "\/") + "\.mjs" :
                null

            // set name, path into array map
            context = context.startsWith("\.") ? context.split("\.").pop() : context
            return new Map([
                [ context, path ]
            ])
        })

    let mainClass = await Promise.all(
        pathClass.reduce(function (o, sourceMap) {

            // get name, path from array map
            let [ nameClass, sourceClass ] = Array.from(sourceMap.entries())[0]

            o.push((async function() {

                // load module
                let module = await import(sourceClass)
                return [ nameClass, module ]
            })())

            return o
        }, [])
    )

    function convertArrayToObject(array, o) {
        let obj = {}
        if (o && typeof o == "object") obj = o
        return array.reduce(function (o, a) {
            let [ key, value ] = a
            Object.defineProperty(o, key, {
                configurable: true,
                enumerable: true,
                writable: false,
                value: value
            })
            return o
        }, obj)
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

            let main = mainClass.map(function (link) {

                let callback = null
                let [ name, program ] = link
                let main = program.default
                if (typeof main == "function") {
                    if (main.name === name) {
                        if ("Main" in main) {
                            if (typeof main.Main == "function") {

                                convertArrayToObject(query, main)
                                callback = main.Main.bind(main)
                            }
                        }
                    } else {

                        throw`mainClass ${name} not same as file name!`
                    }
                }

                return callback
            })

            // execute all main
            for (let c of main) {

                if (c && typeof c == "function") c()
            }

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