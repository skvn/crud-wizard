export function makeid () {
  let text = ''
  const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'

  for (let i = 0; i < 5; i++) {
    text += possible.charAt(Math.floor(Math.random() * possible.length))
  }
  return text
}

export function classes (options) {
  var cls = []
  for (var p in options) {
    if (options[p])
      cls.push(p)
  }
  return cls
}
