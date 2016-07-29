<template>

    <a href="#" class="btn btn-success" @click.prevent="$broadcast('scope::new')"><i class="fa fa-plus"></i>
        Add scope</a>
    <br />
    <br />
    <table class="table table-striped">
        <tr>
            <th width="100px">Alias</th>
            <th width="200px">Title</th>
            <th>Columns</th>
            <th width="100px"></th>
        </tr>
        <tr v-for="(key, s) in $parent.model.scopes">
            <td>{{ key }}</td>
            <td>{{ s.title }}</td>
            <td>{{ getListCols(s.list).join(', ') }}</td>
            <td><a class="text-info" style="font-size: 20px;" href="#" @click.prevent="$broadcast('scope::edit', key)"><i
                    class="fa fa-edit"> </i></a>
                &nbsp;&nbsp;&nbsp;
                <a class="text-danger" href="#" @click.prevent="deleteScope(key)"
                   style="font-size: 20px;"><i class="fa fa-trash-o"> </i></a>
            </td>
        </tr>
    </table>

    <a href="#" class="btn btn-success" @click.prevent="$broadcast('scope::new')"><i class="fa fa-plus"></i>
        Add scope</a>

   <scope-edit></scope-edit>

</template>

<script>
import Vue from 'vue';
import ScopeEdit from './ScopeEdit.vue'
import { getConfig, getModel } from '../vuex/getters'

    export default{
        components:{
            ScopeEdit
        },

        vuex: {
            getters: {
                config: getConfig,
                model: getModel
            }

        },

        data(){
            return {
            }
        },
        methods: {

            getListCols(list) {

                if (Array.isArray(list)) {
                    return list.map((item) => {
                        return item.data;
                    });
                }
            },

            deleteScope(key) {


                swal(
                        {
                            title: "Are you sure?",
                            text: "You will not be able to recover this scope!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        })
                        .then(() => {

                            Vue.delete(this.model.scopes, key);
                            swal(
                                    'Deleted!',
                                    'The scope has been deleted.',
                                    'success'
                            );
                        }, () => {});


            },

        },

        watch : {

        }

    }
</script>
