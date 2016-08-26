<template>
  <div class="mdl-grid">
    <div class="mdl-cell mdl-cell--6-col">
      <component :is="getFieldType(field.type)" :model.sync="model" :config="field" :key="key"
                 v-for="(key, field) in structure|filterBy 1 in 'col'"></component>
    </div>
    <div class="mdl-cell mdl-cell--6-col">
      <component :is="getFieldType(field.type)" :model.sync="model" :config="field" :key="key"
                 v-for="(key, field) in structure|filterBy 2 in 'col'"></component>
    </div>
    <div class="mdl-cell mdl-cell--6-col">
      <component :is="getFieldType(field.type)" :model.sync="model" :config="field" :key="key"
                 v-for="(key, field) in structure|filterBy 3 in 'col'"></component>
    </div>
  </div>
</template>
<script>
  import FormLayoutMixin from './mixins/FormLayoutMixin'

  export default {
    mixins: [FormLayoutMixin]
  }
</script>
