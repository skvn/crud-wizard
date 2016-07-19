<template>

    <add-field-btn></add-field-btn>

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
            <td><a class="text-info" style="font-size: 20px;" href="#" @click="editField(key)"><i
                    class="fa fa-edit"> </i></a>
                &nbsp;&nbsp;&nbsp;
                <a class="text-danger" href="#" @click.prevent="this.$dispatch('delete_field',key)"
                   style="font-size: 20px;"><i class="fa fa-trash-o"> </i></a>
            </td>
        </tr>
    </table>

   <add-field-btn></add-field-btn>

   <!--<relation-modal></relation-modal>-->

</template>

<script>

//import RelationModal from './RelationModal.vue'
import AddFieldBtn from './stubs/AddFieldBtn.vue'

    export default{
        components:{
            AddFieldBtn
        },

        data(){
            return {
                new_field_type:''
            }
        },
        methods: {

            addField() {
                if (this.new_field_type == '') {
                    swal('Choose field type','','warning');
                    return false;
                }
//                this.$broadcast('relation::new', this.new_relation_type);
//                this.$broadcast('show::modal', 'relation_modal');
//                this.new_relation_type = '';
            },

            isField (row) {
                if (!row.$value.relation)
                {
                    return row;
                }
            },
        }

    }
</script>
