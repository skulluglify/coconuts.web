$(function () {

    let selectYear = $('select[name=\'year\']')
    let selectMonth = $('select[name=\'month\']')
    let selectDay =  $('select[name=\'day\']')

    let dateInput = new DateInput(selectYear[0], selectMonth[0], selectDay[0])
    dateInput.init()
})

class DateInput extends Object {

    selectYear = null;
    selectMonth = null;
    selectDay = null;

    constructor(selectYear, selectMonth, selectDay) {
        super();

        if (HTMLSelectElement.prototype.isPrototypeOf(selectYear)) this.selectYear = selectYear
        if (HTMLSelectElement.prototype.isPrototypeOf(selectMonth)) this.selectMonth = selectMonth
        if (HTMLSelectElement.prototype.isPrototypeOf(selectDay)) this.selectDay = selectDay
    }

    init() {

        if (!this.selectYear) throw`year selection not null!`
        if (!this.selectMonth) throw`month selection not null!`
        if (!this.selectDay) throw`day selection not null!`

        this.removeAllChild(this.selectYear)
        this.removeAllChild(this.selectMonth)
        this.removeAllChild(this.selectDay)

        let date = new Date()

        let Y, M, D;
        Y = date.getFullYear() // 4 digits
        M = date.getMonth()
        D = date.getDate()

        let monNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'
        ]

        for (let i in monNames) {
            let monName = monNames[i]
            let option = document.createElement('option')
            option.textContent = monName
            if (i == M) option.setAttribute('selected', '')
            this.selectMonth.appendChild(option)
        }

        let y = 1998;
        while (y <= Y) {

            let option = document.createElement('option')

            for (let span of this.tabularNumSpan(y))
                option.appendChild(span)

            if (y == Y) option.setAttribute('selected', '')
            this.selectYear.appendChild(option)

            y = y + 1 ;
        }
    }

    removeAllChild(elem) {

        if (HTMLElement.prototype.isPrototypeOf(elem)) {

            for (let child of Array.from(elem))
                if (!!child) child.remove()
        }
    }

    *tabularNumSpan(value) {

        let context = value.toString()

        for (let c of context) {

            let span = document.createElement('span')
            span.textContent = c
            span.style.width = '0.75em'
            span.style.fontStyle = 'bold'
            span.style.border = '1px solid blue'
            yield span
        }
    }
}