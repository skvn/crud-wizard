<template>
  <mdl-textfield :floating-label="config.label" :value.sync="model[key]"></mdl-textfield>
</template>

<script>

  import FieldMixin from './mixins/FieldMixin'

  export default {

   mixins: [FieldMixin]

  }
</script>
