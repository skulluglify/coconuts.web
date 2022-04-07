export class Bindings {

    moduleName

    constructor(moduleName) {

        if (moduleName && typeof moduleName == "string")
            this.moduleName = moduleName
    }

    toString() {

        if ("moduleName" in this)
            return "Bindings {" + this.moduleName + "}"

        return "Bindings {Unknown}"
    }

    toLocaleString() {

        return this.toString()
    }
}

export default class JessieQuery extends Object {

    cls = null;

    constructor(cls = null) {

        super()

        if (typeof cls == "function")
            this.cls = cls
    }


    xQuery(context) {

        if (Window.prototype.isPrototypeOf(context)) {}
        if (Document.prototype.isPrototypeOf(context)) {}
        if (Element.prototype.isPrototypeOf(context)) {}
        if (typeof context == "string" && context.length > 0) {}
        if (context && Array.isArray(context) && context.length > 0) {}
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

    __Module__(module) {

        // If Module Is ES6 (ImportLib), Call Then Try Again
        if (module && Promise.prototype.isPrototypeOf(module)) {

            // Await Promise Is Done
            module.then((function (module) {

                // Try Again
                this.__Module__(module)

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

            // Get Init Name
            let initName = null
            if (module.name.length > 0) {

                let codepoint = module.name.codePointAt(0)

                if (65 <= codepoint && codepoint <= 90)
                    initName = "Init" + module.name
                else
                    throw "[JessieQuery] Module Name Require Capitalize Each Word!"
            }

            // Check if is Ready Load
            if (!(initName && initName in module))
                throw "[JessieQuery] Require " + initName + " for MainInit!"

            // get Collection Into Activity
            if ("Collections" in module) {

                let Collections = module.Collections

                if (typeof Collections == "function") {

                    // Binding And Calling
                    let collections = Collections.call(module)

                    if (collections && Array.isArray(collections) && collections.length > 0) {

                        this.cls[module.name] = new Bindings(module.name) // Empty Bindings
                        let bindings = this.cls[module.name]

                        bindings["Init"] = module[initName].bind(module)

                        for (let func of collections) {

                            // Since Module "Activity" disable from ImportLib
                            // if (!(module.name == "Activity" && func.name == "Main"))
                            //     throw "[JessieQuery][ImportLib] MainClass from Activity not enable!"
                            if (func.name == "Main")
                                throw "[JessieQuery][ImportLib] Couldn't Import MainClass!"
                            else // Disable MainInit
                                if (func.name == initName)
                                    throw "[JessieQuery][ImportLib] MainInit Has Been Imported!"
                            else // Binding Into Activity
                                if (typeof func == "function")
                                    bindings[func.name] = func.bind(module)
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

    /**
     * @param {any} module
     * */
    isBindings(module) {

        return module && Bindings.prototype.isPrototypeOf(module)
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

                            this.__Module__(module)
                        }
                    }
            }).bind(ThisObject) // More Safety
        }
    }

    // NO WRITABLE
    set Module(e) {}
}