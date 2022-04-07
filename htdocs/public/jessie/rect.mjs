export class Cartesian {

    X
    Y

    constructor(x, y) {

        this.X = x
        this.Y = y
    }

    toString() {

        return "Cartesian {X: " + this.X + ", Y: " + this.Y + "}"
    }

    toLocaleString() {

        return this.toString()
    }
}


export class Corner {

    TopStart
    TopEnd
    BottomStart
    BottomEnd

    // top left width height
    constructor(t, l, w, h) {

        this.TopStart = new Cartesian(l, t)
        this.TopEnd = new Cartesian(l + w, t)
        this.BottomStart = new Cartesian(l, t + h)
        this.BottomEnd = new Cartesian(l + w, t + h)
    }

    toString() {

        return "Corner {TopStart: " + this.TopStart + ", TopEnd: " + this.TopEnd + ", BottomStart: " + this.BottomStart + ", BottomEnd: " + this.BottomEnd + "}"
    }

    toLocaleString() {

        return this.toString()
    }
}

export default class Rect {

    Top
    Left
    Width
    Height
    Corner // topStart, topEnd, bottomStart, bottomEnd
    Cartesian // X, Y

    constructor(t, l, w, h) {

        // commonly
        this.Top = t
        this.Left = l
        this.Width = w
        this.Height = h

        this.Corner = new Corner(t, l, w, h)

        let x = l + (w / 2)
        let y = t + (h / 2)
        this.Cartesian = new Cartesian(x, y)
    }

    static getElementRect(target) {

        if (target && HTMLElement.prototype.isPrototypeOf(target)) {

            let top = target.clientTop || target.offsetTop
            let left = target.clientLeft || target.offsetLeft
            let width = target.clientWidth || target.offsetWidth
            let height = target.clientHeight || target.offsetHeight

            return new Rect(top, left, width, height)
        }
    }

    toString() {

        return "Rect {Top: " + this.Top + ", Left: " + this.Left + ", Width: " + this.Width + ", Height: " + this.Height + ", Corner: " + this.Corner + ", Cartesian: " + this.Cartesian + "}"
    }

    toLocaleString() {

        return this.toString()
    }
}