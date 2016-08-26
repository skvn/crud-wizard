<template>
  <div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col">
      <mdl-select label="Choose relation type" :options="$parent.commonConfig.relation_options" :value.sync="type"></mdl-select>
    </div>
    <div class="mdl-cell mdl-cell--2-col">
      <mdl-button v-mdl-ripple-effect icon  colored  @click="this.$dispatch('relation::add', type)">
        <i class="material-icons">add</i> Add relation
      </mdl-button>
    </div>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        type: ''
      }
    }
  }
</script>
