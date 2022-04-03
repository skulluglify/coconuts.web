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
                document.head.appendChild(styleEmpty)
                styleSheet = document.styleSheets.item(0)

                if (!styleSheet) throw "cannot create style sheet!"
            }

            if (styleSheet) {

                styleSheet.insertRule(rule, styleSheet.cssRules.length)
            }
        }
    }
}