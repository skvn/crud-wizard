<template>

  <component :is="formLayout" :structure="structure" :model.sync="model"></component>

</template>
<script>
  import LayoutOneColumn from './LayoutOneColumn.vue'
  import LayoutTwoColumn from './LayoutTwoColumn.vue'
  import LayoutThreeColumn from './LayoutThreeColumn.vue'

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
      LayoutTwoColumn,
      LayoutThreeColumn
    },
    data () {
      return {
        formLayout: 'LayoutOneColumn'
      }
    },
    created () {
      let numCols = 1
      for (var key in this.structure) {
        if (this.structure.hasOwnProperty(key)) {
          if (this.structure[key]['col'] && this.structure[key]['col'] > numCols) {
            numCols = this.structure[key]['col']
          }
        }
      }
      switch (numCols) {
        case 2:
          this.formLayout = 'LayoutTwoColumn';
          break
        case 3:
          this.formLayout = 'LayoutThreeColumn';
          break

      }
    }
  }
</script>
