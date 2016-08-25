<template>
  <component :is="getFieldType(field.type)" :model.sync="model" :config="field" :key="key" v-for="(key, field) in structure"></component>
</template>
<script>
  import FormLayoutMixin from './mixins/FormLayoutMixin'

  export default {
    mixins: [FormLayoutMixin]
  }
</script>
