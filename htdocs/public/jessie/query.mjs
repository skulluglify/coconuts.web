export default class JessieQuery extends Object {

    constructor() {

        super()
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

    loadModule(cls) {

        // if cls is es6 importModule As Promise<Awaited>
        if (cls && Promise.prototype.isPrototypeOf(cls)) {

            cls.then((function (module) {

                // call itself
                this.loadModule(module)
            }).bind(this)).catch(function (err) {

                throw "[JessieQuery] Couldn't load Module from Activity!"
                throw err
            })

            return null
        }

        // if cls is es6 module
        cls = "default" in cls ? cls.default : cls

        if (cls && typeof cls == "function") {

            // Embedded JessieQuery in Class<Object>
            Object.defineProperty(cls, "jessieQuery", {
                value: new JessieQuery,
                configurable: true,
                enumerable: false,
                writable: false
            })

            // callback Main func
            if ("Main" in cls) cls.Main()
            else throw "[JessieQuery] Not Contains MainClass!"
        }

        return null
    }
}