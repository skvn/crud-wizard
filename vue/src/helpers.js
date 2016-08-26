// export function makeid () {
//   let text = ''
//   const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
//
//   for (let i = 0; i < 5; i++) {
//     text += possible.charAt(Math.floor(Math.random() * possible.length))
//   }
//   return text
// }

Array.prototype.move = function (from, to) {
  this.splice(to, 0, this.splice(from, 1)[0])
}

Array.prototype.uniqueMerge = function (a) {
  for (var nonDuplicates = [], i = 0, l = a.length; i < l; ++i) {
    if (this.indexOf(a[i]) === -1) {
      nonDuplicates.push(a[i])
    }
  }
  return this.concat(nonDuplicates)
}
