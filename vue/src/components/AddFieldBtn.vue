<template>


  <a v-if="!show_block" href="#" class="btn btn-success" @click.prevent="show_block = true"><i class="fa fa-plus"></i>
    Add field</a>

  <label v-else>
    <select class="form-control default_select" style="display:inline; width:250px;" v-model="$parent.new_field_key">
      <option value="">Choose existing database table field</option>
      <option v-for="f in $parent.$parent.config.table_columns | filterBy notUsedField" v-bind:value="f">
        {{ f }}
      </option>
    </select>
    or add a new one <input type="text" style="display:inline; width:150px;" v-model="$parent.new_field_key_new"/>

    <select class="form-control default_select" id="rtype" v-model.sync="$parent.new_field_type"
            style="display:inline; width:250px;">
      <option value="">field type</option>

      <option v-for="(val, text) in $parent.$parent.config.field_options" v-bind:value="val">
        {{ text }}
      </option>

    </select>
    <button class="btn btn-primary" @click.prevent="$parent.addField()">Ok</button>


  </label>

</template>

<script>

  import {getModel} from '../vuex/getters'

  export default{

    data(){
      return {
        show_block: false
      }
    },

    vuex: {
      getters: {
        model: getModel
      }

    },

    methods: {
      notUsedField(row) {

        if (typeof this.model.fields[row] == 'undefined') {
          return true;
        }

        return false;
      },

    },


  }
</script>
