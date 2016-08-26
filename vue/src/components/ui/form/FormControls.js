import TextField from './Text.vue'
import SelectField from './Select.vue'
import SwitchField from './Switch.vue'

export const components = {
  TextField,
  SelectField,
  SwitchField
}

export default {
  install (Vue) {
    Object.keys(components).forEach((name) => {
      Vue.component(name, components[name])
    })
  }
}

export {
  TextField,
  SelectField,
  SwitchField
}