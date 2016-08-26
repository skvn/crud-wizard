<template>
  <div class="mdl-grid mdl-grid--no-spacing">
    <div class="mdl-cell mdl-cell--4-col" v-show="chooseType">
      <mdl-select @change="" label="Choose relation type" :options="$parent.commonConfig.relation_options" :value.sync="type"></mdl-select>
    </div>
    <div class="mdl-cell mdl-cell--2-col" style="padding-top:15px;">
      <mdl-button v-mdl-ripple-effect icon  colored  @click="chooseType=true" v-if="!chooseType">
        <i class="material-icons">add</i> Add relation
      </mdl-button>
      <mdl-button v-mdl-ripple-effect icon colored accent   @click="chooseType=false" v-if="chooseType">
        <i class="material-icons">cancel</i> Cancel
      </mdl-button>

    </div>
  </div>
</template>
<style>
  .mdl-textfield__label {
    color: #0d47a1;
  }
</style>
<script>
  export default {
    data () {
      return {
        type: '',
        chooseType: false,
      }
    },
    methods: {
      addNew () {
        this.chooseType=false
        this.$dispatch('relation::add', type)
        this.type = ''
      }
    }
  }
</script>
