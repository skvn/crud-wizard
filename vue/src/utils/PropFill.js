export default {
  beforeCompile () {
    if (!this._props) return
    for (let prop of Object.keys(this._props)) {
      let data = this._props[prop]
      if (data.options.fill && data.raw === '') {
        this[prop] = prop
      }
    }
  }
}