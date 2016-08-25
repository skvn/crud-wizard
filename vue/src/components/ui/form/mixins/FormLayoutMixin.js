import TextField from '../Text.vue'
import SelectField from '../Select.vue'
import SwitchField from '../Switch.vue'

export default {
  props: {
    model: {
      type: [Object],
      required: true,
      twoWay: true
    },
    structure: {
      type: [Object],
      required: true
    }
  },
  components: {
    TextField,
    SelectField,
    SwitchField
  },
  data () {
    return {
    }
  },
  methods: {
    getFieldType (type) {
      return type.charAt(0).toUpperCase() + type.slice(1) + 'Field'
    }
  }
}
