// <div className="date-option">
//     <div className="date-year">
//         <div className="option"></div>
//     </div>
//     <div className="date-mon">
//         <div className="option"></div>
//     </div>
//     <div className="date-day">
//         <div className="option"></div>
//     </div>
// </div>

export default class DateOption {

    static date // Proprietary Method Class ItSelf
    static dateDefault // Proprietary Method Class ItSelf
    static jessieQuery // JessieQuery Module Bindings Allocated Memory

    static Main() {

        this.InitDateOption()
    }

    static InitDateOption() {

        this.date = new Date
        this.dateDefault = { // Limit 24 Years Ago From Now
            year: this.date.getFullYear() - 24,
            maxDay: 30, // Not Precisely
            mon: 1, // Start At 1, But DateObject Start At 0
            day: 1 // Start At 1, Link From Day Of Month
        }

        // let checkNodes = document.querySelectorAll("div.date-option")
        //
        // if (checkNodes.length > 0)
        //     Array.from(checkNodes).forEach((function (node) {
        //
        //         // default value
        //         node.dateSelected = Object.assign({}, this.dateDefault)
        //
        //         this.InitYear(node)
        //         this.InitMon(node)
        //         this.InitDay(node)
        //
        //     }).bind(this))

        // Date Option Just Once Call
        let dateOptionEl = document.querySelector("div.date-option")

        if (dateOptionEl && HTMLElement.prototype.isPrototypeOf(dateOptionEl)) {

            // set Default, Important
            dateOptionEl.dateSelected = Object.assign({}, this.dateDefault)

            this.InitYear(dateOptionEl)
            this.InitMon(dateOptionEl)
            this.InitDay(dateOptionEl)
        }
    }

    static getDateValue() {

        let date = {

            year: this.date.getFullYear(),
            mon: this.date.getMonth(),
            day: this.date.getDate()
        }

        let dateOptionEl = document.querySelector("div.date-option")

        if (dateOptionEl && HTMLElement.prototype.isPrototypeOf(dateOptionEl)) {

            if ("dateSelected" in dateOptionEl) {

                let Y = dateOptionEl.dateSelected.year
                let M = dateOptionEl.dateSelected.mon
                let D = dateOptionEl.dateSelected.day

                if (Y !== this.dateDefault.year) date.year = Y
                if (M !== this.dateDefault.mon) date.mon = M
                if (D !== this.dateDefault.day) date.day = D
            }
        }

        // Make Result As String
        let context = ""

        context += date.year.toString() + "\-"
        context += this.TextPad(date.mon.toString(), "0", 2) + "\-"
        context += this.TextPad(date.day.toString(), "0", 2)

        // push push push
        return context
    }

    // Binding Into Activity With JessieQuery
    static Collections() {

        return [

            this.getDateValue,
            this.TextPad
        ]
    }

    static TextRender(node, context) {

        if (node && HTMLElement.prototype.isPrototypeOf(node)) {

            if (typeof context == "string" && context.length > 0) {

                for (let c of context) {

                    let div = document.createElement("div")
                    let span = document.createElement("span")

                    div.classList.add("text")

                    span.textContent = c
                    node.appendChild(div)
                    div.appendChild(span)
                }
            }
        }
    }

    /**
     * @param {string, number} context
     * @param {string, number} zchar
     * @param {number} pad
     * @param {boolean} left
     * */
    static TextPad(context, zchar, pad, left = true) {

        // Auto Convert Into String
        if (typeof context == "number" && !isNaN(context) && isFinite(context)) context = context.toString()
        if (typeof zchar == "number" && !isNaN(zchar) && isFinite(zchar)) zchar = zchar.toString()

        if (typeof context == "string" && typeof zchar == "string" && typeof pad == "number" && typeof left == "boolean" && !isNaN(pad) && isFinite(pad)) {

            let n = pad - context.length
            for (let i = 0; i < n; i++) {

                context = left ? zchar + context : context + zchar
            }
        }

        return context
    }

    static ShowToggle(target) {

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            target.style.display = "none"

            let visible = false

            Array.from(["click", "focusout"]).forEach(function (event) {

                let parent = target.parentNode

                if (parent && HTMLElement.prototype.isPrototypeOf(parent))
                    parent.addEventListener(event, function () {

                        if (!visible) {

                            if (event == "click") {

                                target.style.display = "block"
                                visible = true
                            }

                        } else {

                            setTimeout(function () {

                                target.style.display = "none"
                                visible = false
                            }, 2e2)
                        }
                    })
            })
        }
    }

    static InitYear(e) {

        let year = this.date.getFullYear()
        if (e && HTMLElement.prototype.isPrototypeOf(e)) {

            let parent = e.querySelector("div.date-year")
            let target = parent.querySelector("button.select")

            if (target) {

                this.TextRender(target, year.toString())

                // initialize data selected
                if (!"dateSelected" in e) e.dateSelected = Object.assign({}, this.dateDefault)
                e.dateSelected.year = year

                // get Ma Day of Mon
                let Y = e.dateSelected.year
                let M = e.dateSelected.mon
                let K = !(Y % 4) ? (Y % 400 && !(Y % 100) ? 28 : 29) : 28
                let zDay = M !== 2 ? (!((M < 8 ? M : M - 7) % 2) ? 30 : 31) : K
                e.dateSelected.maxDay = zDay

                this.InitYearOption(parent, e.dateSelected)
            }
        }
    }

    /**
    * @param {Element} e
    * @param {Object, null} o
    */
    static InitYearOption(e, o = null) {

        if (e && HTMLElement.prototype.isPrototypeOf(e)) {

            let date = o || this.dateDefault

            let target = e.querySelector("div.option")

            if (target) {

                this.ShowToggle(target)

                for (let i = date.year; i >= this.dateDefault.year; i--) {

                    let div = document.createElement("button")

                    div.classList.add("select")

                    target.appendChild(div)

                    div.addEventListener("click", (function () {

                        o.year = i
                        let select = e.querySelector("button.select")
                        if (select && HTMLElement.prototype.isPrototypeOf(select)) {

                            select.innerHTML = ""
                            this.TextRender(select, o.year.toString())
                        }

                        // get Ma Day of Mon
                        let Y = "year" in o ? o.year : null
                        let M = "mon" in o ? o.mon : null

                        if (Y && M) {

                            let K = !(Y % 4) ? (Y % 400 && !(Y % 100) ? 28 : 29) : 28
                            let zDay = M !== 2 ? (!((M < 8 ? M : M - 7) % 2) ? 30 : 31) : K
                            o.maxDay = zDay
                        }

                        // Restart Date Day
                        let dateOptionEl = e.parentNode.querySelector("div.date-day")

                        if (dateOptionEl) {

                            let dateDaySelect = dateOptionEl.querySelector("button.select")
                            let dateDayOption = dateOptionEl.querySelector("div.option")

                            if (dateDaySelect && dateDayOption) {

                                dateDaySelect.innerHTML = ""
                                dateDayOption.innerHTML = ""

                                this.InitDay(e.parentNode)
                            }
                        }

                        // push data into dataset
                        if ("dataset" in e && DOMStringMap.prototype.isPrototypeOf(e.dataset)) e.dataset.year = o.year

                    }).bind(this))

                    this.TextRender(div, i.toString())
                }
            }
        }
    }

    static InitMon(e) {

        let mon = this.date.getMonth() + 1 // Start At 0 ~ 11
        let monNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ]
        if (e && HTMLElement.prototype.isPrototypeOf(e)) {

            let parent = e.querySelector("div.date-mon")
            let target = parent.querySelector("button.select")

            if (target) {

                this.TextRender(target, monNames[mon - 1])

                if (!"dateSelected" in e) e.dateSelected = Object.assign({}, this.dateDefault)
                e.dateSelected.mon = mon

                // inject data Object into Element
                parent.monNames = monNames

                // get Ma Day of Mon
                let Y = e.dateSelected.year
                let M = e.dateSelected.mon
                let K = !(Y % 4) ? (Y % 400 && !(Y % 100) ? 28 : 29) : 28
                let zDay = M !== 2 ? (!((M < 8 ? M : M - 7) % 2) ? 30 : 31) : K
                e.dateSelected.maxDay = zDay

                this.InitMonOption(parent, e.dateSelected)
            }
        }
    }

    /**
     * @param {Element} e
     * @param {Object, null} o
     */
    static InitMonOption(e, o = null) {

        if (e && HTMLElement.prototype.isPrototypeOf(e)) {

            // let date = o || this.dateDefault
            let monNames = "monNames" in e ? e.monNames : []

            let target = e.querySelector("div.option")

            if (target) {

                this.ShowToggle(target)

                if (monNames.length === 12) {

                    for (let i = this.dateDefault.mon; i <= 12; i++) {

                        let div = document.createElement("button")

                        div.classList.add("select")

                        target.appendChild(div)

                        div.addEventListener("click", (function () {

                            o.mon = i
                            let select = e.querySelector("button.select")
                            if (select && HTMLElement.prototype.isPrototypeOf(select)) {

                                select.innerHTML = ""
                                this.TextRender(select, monNames[o.mon - 1])
                            }

                            // get Ma Day of Mon
                            let Y = "year" in o ? o.year : null
                            let M = "mon" in o ? o.mon : null

                            if (Y && M) {

                                let K = !(Y % 4) ? (Y % 400 && !(Y % 100) ? 28 : 29) : 28
                                let zDay = M !== 2 ? (!((M < 8 ? M : M - 7) % 2) ? 30 : 31) : K
                                o.maxDay = zDay
                            }

                            // Restart Date Day
                            let dateOptionEl = e.parentNode.querySelector("div.date-day")

                            if (dateOptionEl) {

                                let dateDaySelect = dateOptionEl.querySelector("button.select")
                                let dateDayOption = dateOptionEl.querySelector("div.option")

                                if (dateDaySelect && dateDayOption) {

                                    dateDaySelect.innerHTML = ""
                                    dateDayOption.innerHTML = ""

                                    this.InitDay(e.parentNode)
                                }
                            }

                            // push data into dataset
                            if ("dataset" in e && DOMStringMap.prototype.isPrototypeOf(e.dataset)) e.dataset.mon = o.mon

                        }).bind(this))

                        this.TextRender(div, monNames[i - 1])
                    }
                }
            }
        }
    }

    static InitDay(e) {

        let date = "dateSelected" in e ? e.dateSelected : this.dateDefault
        let day = date.day !== this.dateDefault.day ? date.day : this.date.getDate() // local time

        // fix day Num
        day = date.maxDay < day ? date.maxDay : day
        date.day = day

        if (e && HTMLElement.prototype.isPrototypeOf(e)) {

            let parent = e.querySelector("div.date-day")
            let target = parent.querySelector("button.select")

            if (target) {

                this.TextRender(target, this.TextPad(day.toString(), "0", 2))

                if (!"dateSelected" in e) e.dateSelected = Object.assign({}, this.dateDefault)
                e.dateSelected.day = day

                this.InitDayOption(parent, e.dateSelected)
            }
        }
    }

    /**
     * @param {Element} e
     * @param {Object, null} o
     */
    static InitDayOption(e, o = null) {

        if (e && HTMLElement.prototype.isPrototypeOf(e)) {

            let date = o || this.dateDefault

            let target = e.querySelector("div.option")

            if (target) {

                this.ShowToggle(target)

                // let Y = date.year
                // let M = date.mon
                // let K = !(Y % 4) ? (Y % 400 && !(Y % 100) ? 28 : 29) : 28
                // let Z = M !== 2 ? (!((M < 8 ? M : M - 7) % 2) ? 30 : 31) : K

                let zDay = "maxDay" in o ? o.maxDay : this.dateDefault.maxDay

                for (let i = this.dateDefault.day; i <= zDay; i++) {

                    let div = document.createElement("button")

                    div.classList.add("select")

                    target.appendChild(div)

                    div.addEventListener("click", (function () {

                        o.day = i
                        let select = e.querySelector("button.select")
                        if (select && HTMLElement.prototype.isPrototypeOf(select)) {

                            select.innerHTML = ""
                            this.TextRender(select, this.TextPad(i.toString(), "0" , 2))
                        }

                        // push data into dataset
                        if ("dataset" in e && DOMStringMap.prototype.isPrototypeOf(e.dataset)) e.dataset.day = o.day

                    }).bind(this))

                    this.TextRender(div, this.TextPad(i.toString(), "0", 2))
                }
            }
        }
    }
}