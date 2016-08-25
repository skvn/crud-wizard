<template>

  <component :is="formLayout" :structure="structure" :model.sync="model"></component>

</template>
<script>
  import LayoutOneColumn from './LayoutOneColumn.vue';
  import LayoutTwoColumn from './LayoutTwoColumn.vue';

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
      LayoutOneColumn,
      LayoutTwoColumn
    },
    data () {
      return {
        formLayout: 'LayoutOneColumn'
      }
    },
    created () {
      console.log('BBB')
      console.log(this.structure)
    }
  }
</script>
