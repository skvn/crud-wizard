<template>
  <mdl-switch :checked.sync="model[key]" class="mdl-js-ripple-effect">{{ config.label }}</mdl-switch>
</template>

<script>
  import FieldMixin from './mixins/FieldMixin'

  export default {
    mixins: [FieldMixin]
  }
</script>
