<template>
  <table class="mdl-data-table">

    <thead v-if="header">
    <tr>

      <th v-for="h in header" class="tdClass(h)">
        {{ h.title }}
      </th>
    </tr>
    </thead>

    <tbody>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">Acrylic (Transparent)</td>
      <td>25</td>
      <td>$2.90</td>
    </tr>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">Plywood (Birch)</td>
      <td>50</td>
      <td>$1.25</td>
    </tr>
    <tr>
      <td class="mdl-data-table__cell--non-numeric">Laminate (Gold on Blue)</td>
      <td>10</td>
      <td>$2.35</td>
    </tr>
    </tbody>
  </table>
</template>
<script>
  import PropFill from '../../utils/PropFill'

  export default {
    props: {
      header: {
        type: [Object, Boolean],
        required: false,
        default: false
      },
      select: {
        type: [Object, Boolean],
        required: false,
        default: false
      },
    },

    data(){
      return {
        tableClass: ''
      }
    },

    ready () {
      componentHandler.upgradeElement(this.$el)
      if
      mdl-js-data-table     mdl - data - table--
      selectable
      mdl - shadow--
      2
      dp
    },

    methods: {
      tdClass (td) {
        if (td.numeric || td.number) {
          return '';
        }
        return "mdl-data-table__cell--non-numeric"
      }
    },

    mixins: [PropFill]
  }
</script>