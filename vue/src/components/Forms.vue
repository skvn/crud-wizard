<template>

  <a href="#" class="btn btn-success" @click.prevent="$broadcast('form::new')"><i class="fa fa-plus"></i>
    Add form</a>
  <br/>
  <br/>
  <table class="table table-striped">
    <tr>
      <th width="200px">Form alias</th>
      <th width="100px">Type</th>
      <th>Fields</th>
      <th width="100px"></th>
    </tr>
    <tr v-for="(key, f) in $parent.model.forms">
      <td>{{ key }}</td>
      <td>{{ getFormType(f) }}</td>
      <td>{{ getFormFields(f).join(', ') }}</td>
      <td><a class="text-info" style="font-size: 20px;" href="#" @click.prevent="$broadcast('form::edit', key)"><i
              class="fa fa-edit"> </i></a>
        &nbsp;&nbsp;&nbsp;
        <a class="text-danger" href="#" @click.prevent="deleteForm(key)"
           style="font-size: 20px;"><i class="fa fa-trash-o"> </i></a>
      </td>
    </tr>
  </table>

  <a href="#" class="btn btn-success" @click.prevent="$broadcast('form::new')"><i class="fa fa-plus"></i>
    Add form</a>

  <form-edit></form-edit>

</template>

<script>
  import Vue from 'vue';
  import FormEdit from './FormEdit.vue'
  import {getConfig, getModel} from '../vuex/getters'

  export default{
    components: {
      FormEdit
    },

    vuex: {
      getters: {
        config: getConfig,
        model: getModel
      }

    },

    data(){
      return {}
    },
    methods: {

      getFormType(form) {
        if (Array.isArray(form)) {
          return 'simple';
        } else if (Object.keys(form).length) {
          return 'tabbed'
        }


      },

      getFormFields(form) {
        if (this.getFormType(form) == 'tabbed') {

          var ret = [];
          for (let i in form) {
            if (typeof form[i]['fields'] != 'undefined') {
              ret = ret.concat(form[i]['fields']);
            }
          }

          return ret;
        }
        return form;
      },

      deleteForm(key) {


        swal(
                {
                  title: "Are you sure?",
                  text: "You will not be able to recover this form!",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes, delete it!"
                })
                .then(() => {

                  Vue.delete(this.model.forms, key);
                  swal(
                          'Deleted!',
                          'The form has been deleted.',
                          'success'
                  );
                }, () => {
                });


      },

    },

    watch: {}

  }
</script>
