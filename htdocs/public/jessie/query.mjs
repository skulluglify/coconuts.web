export default class JessieQuery extends Object {

    cls = null;

    constructor(cls = null) {

        super()

        if (typeof cls == "function")
            this.cls = cls
    }

    /**
     * @param {string} rule
     */
    styleInsertRule(rule) {

        if (document.readyState === "complete") {

            let styleSheet = document.styleSheets.item(0)

            // handle if null
            if (!styleSheet) {

                let styleEmpty = document.createElement("style")
                document.head.appendChild(styleEmpty) // Registry In Head Document
                styleSheet = styleEmpty.sheet // Get Sheet From Style Element

                if (!styleSheet) throw "[JessieQuery] Couldn't Create StyleSheets!"
            }

            if (styleSheet) {

                styleSheet.insertRule(rule, styleSheet.cssRules.length)
            }
        }
    }

    __addModule__(module) {

        // If Module Is ES6 (ImportLib), Call Then Try Again
        if (module && Promise.prototype.isPrototypeOf(module)) {

            // Await Promise Is Done
            module.then((function (module) {

                // Try Again
                this.__addModule__(module)

            }).bind(this)).catch(function (err) {

                throw "[JessieQuery] Couldn't load Module from Activity!"
                throw err
            })

            return null
        }

        // get Export Default
        // If Module Is ES6 (ImportLib)
        module = "default" in module ? module.default : module

        // Exclude "Activity" Class Name
        // Because "Activity" Class Name Only For Single Main Class
        // Default Is Function, Free Class Name (Not Require "Activity" Name)
        if (module && typeof module == "function" && module.name !== "Activity") {

            // Embedded JessieQuery in Class<Object>
            Object.defineProperty(module, "jessieQuery", {
                value: new JessieQuery(module), // Enable Bindings Recursive
                configurable: true,
                enumerable: false,
                writable: false
            })

            // get Collection Into Activity
            if ("Collections" in module) {

                let Collections = module.Collections

                if (typeof Collections == "function") {

                    // Binding And Calling
                    let collections = Collections.call(module)

                    if (collections && Array.isArray(collections) && collections.length > 0) {

                        this.cls[module.name] = {} // Empty Bindings
                        let bindings = this.cls[module.name]

                        for (let func of collections) {

                            if (typeof func == "function") {

                                if (func.name in module) {

                                    bindings[func.name] = module[func.name].bind(module)
                                }
                            }
                        }
                    }
                }
            }

            // callback Main func
            if ("Main" in module) module.Main()
            else throw "[JessieQuery] Not Contains MainClass!"
        }

        return null
    }

    get Module() {

        // Break Point Access, No Conflicts
        let ThisObject = this

        return {

            Extends: (function (...modules) { // If Using Lambda, Not Require Binding ThisObject AnyMore

                if (modules.length > 0)
                    for (let module of modules) {

                        // Module If Is Function
                        // Module If Is Promise (ImportLib)
                        if (typeof module == "function" || Promise.prototype.isPrototypeOf(module)) {

                            this.__addModule__(module)
                        }
                    }
            }).bind(ThisObject) // More Safety
        }
    }

    // NO WRITABLE
    set Module(e) {}
}