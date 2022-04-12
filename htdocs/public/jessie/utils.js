export let is = {

    _string: (context) => !!context && typeof context == "string" && context.length > 0,
    _array: (arr) => !!arr && typeof arr == "object" && Array.isArray(arr) && arr.length > 0,
    _object: (obj) => !!obj && typeof obj == "object" && !Array.isArray(obj) && Object.keys(obj).length > 0,
    _element: (node) => !!node && Element.prototype.isPrototypeOf(node),
    _document: (node) => !!node && Document.prototype.isPrototypeOf(node),
    _window: (node) => !!node && Window.prototype.isPrototypeOf(node),
    _empty: (data) => !is._array(data) && !is._object(data) && !is._string(data),
    _symbol: (sym) => !!sym && typeof sym == "symbol",
    _number: (num) => !!num && typeof num == "number",
    _bool: (b) => !!b && typeof b == "boolean",
    _fn: (fun) => !!fun && typeof fun == "function"
}