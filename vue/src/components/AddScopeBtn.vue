<template>
  <label>

    <select class="form-control default_select" style="display:inline; width:250px;" v-model="scope">
      <option value="">Choose model scope</option>
      <option v-for="s in $parent.config.scopes | filterBy notUsedScope" v-bind:value="s.name">
        {{ s.method }} ({{ s.description }})
      </option>
    </select>
    <a href="#" class="btn btn-success" @click.prevent="this.$parent.addScope(scope)"><i class="fa fa-plus"></i>
      Add scope</a>
  </label>
</template>

<script>

  export default{

    data() {
      return {
        scope: ''
      }
    },
    methods: {
      notUsedScope(row) {

        if (typeof this.$parent.model.scopes[row.name] == 'undefined') {
          return true;
        }

        return false;
      },

    },

  }
</script>
