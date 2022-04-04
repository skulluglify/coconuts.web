let windowHint = screenTop
let xz = innerWidth / outerWidth
let toleranceX = 80 <= (xz * 100)
let isFullwindow = screenTop <= 30 && screenLeft <= 0 && toleranceX 
let isDebug = isFullwindow && 251 <= (outerWidth - innerWidth - screenLeft)