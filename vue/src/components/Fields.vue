<template>

    <add-field-btn></add-field-btn>
    <br />
    <br />
    <table class="table table-striped">
        <tr>

            <th width="100px">Attribute name</th>
            <th width="100px">Type</th>
            <th>Title</th>
            <th width="100px"></th>
        </tr>
        <tr v-for="(key, f) in $parent.model.fields | filterBy isField">
            <td>{{ key }}</td>
            <td>{{ f.type }}</td>
            <td>{{ f.title }}</td>
            <td>
                <a title="Clone this field" class="text-success" style="font-size: 20px;" href="#"
                   @click.prevent="cloneField(key)"><i
                        class="fa fa-clone"> </i></a>&nbsp;&nbsp;&nbsp;<a title="Edit field" class="text-info"
                                                                          style="font-size: 20px;" href="#"
                                                                          @click.prevent="editField(key)"><i
                    class="fa fa-edit"> </i></a>&nbsp;&nbsp;&nbsp;<a title="Delete field" class="text-danger" href="#"
                                                                     @click.prevent="this.$dispatch('delete_field',key)"
                                                                     style="font-size: 20px;"><i
                    class="fa fa-trash-o"> </i></a>
            </td>
        </tr>
    </table>

   <add-field-btn></add-field-btn>

   <field-edit></field-edit>

</template>

<script>

import FieldEdit from './FieldEdit.vue'
import AddFieldBtn from './AddFieldBtn.vue'


import { getConfig, getModel } from '../vuex/getters'

    export default{
        components:{
            AddFieldBtn,
            FieldEdit
        },

        vuex: {
            getters: {
                config: getConfig,
                model: getModel
            }

        },

        data(){
            return {
                new_field_type:'',
                new_field_key:'',
                new_field_key_new:''
            }
        },
        methods: {

            addField() {
                if (this.new_field_key == '' && this.new_field_key_new == '') {
                    swal('Oh, no :(','Please, choose an existing table field or enter a new one','warning');
                    return false;
                }

                if (this.new_field_type == '') {
                    swal('Oh no : (','Please, choose a field type','warning');
                    return false;
                }

                if (this.new_field_key_new == '' && this.config.fields_config[this.new_field_type]['is_for_virtual']) {
                    swal('Oh no : (','This field type can be used only with a virtual property. Please enter a new property.','warning');
                    return false;
                }

                this.$broadcast('field::new', {type:this.new_field_type, key: (this.new_field_key || this.new_field_key_new)});
                this.$broadcast('show::modal', 'field_modal');
                this.new_field_type = '';
                this.new_field_key = '';
                this.new_field_key_new = '';
            },

            cloneField(key) {

                this.$broadcast('field::clone', key);
                this.$broadcast('show::modal', 'field_modal');

            },

            editField(key) {
                this.$broadcast('field::edit', key);
                this.$broadcast('show::modal', 'field_modal');
            },

            isField (row) {
                if (!row.$value.relation)
                {
                    return row;
                }
            },
        },

        watch : {
            'new_field_key': function (val, oldVal) {
                if (val != '')
                {
                    this.new_field_key_new = "";
                }
            },

            'new_field_key_new': function (val, oldVal) {
                if (val != '')
                {
                    this.new_field_key = "";
                }
            },

        }

    }
</script>
