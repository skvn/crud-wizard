<template>
  <mdl-select :label="config.label"  :value.sync="model[key]" :options="config.options"></mdl-select>
</template>

<script>
  import FieldMixin from './mixins/FieldMixin'

  export default {
    mixins: [FieldMixin]
  }
</script>
