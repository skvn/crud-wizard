
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
