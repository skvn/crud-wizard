<template>
    <h1 v-show="!model_loaded" ><i  class="fa fa-spinner fa-spin"></i></h1>
    <div :class="{ 'container-fluid in ': model_loaded }" class="container-fluid fade">

        <div class="row">
            <div class="col-md-12">

                <!-- Nav tabs -->
                <div class="card">
                    <div class="pull-left" style="width: 90%">
                        <ul class="nav nav-pills">
                            <li role="presentation" data-rel="/general"><a  v-link="'general'">General settings</a></li>
                            <li role="presentation" data-rel="/relations"><a  v-link="'relations'">Relations</a></li>
                        </ul>
                    </div>
                    <div class="pull-right">
                        <input @click="saveModel()" type="button" class="btn btn-danger" value="Save Model" />
                    </div>
                    <br clear="all" />

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <router-view></router-view>
                    </div>
                </div>
            </div>
        </div>
        <input @click="saveModel()" type="button" class="btn btn-danger" value="Save Model" />


    </div>

</template>
<script>

    import Vue from 'vue'
    import store from '../vuex/store'
    import Actions from '../vuex/actions'
    import { getModel, getConfig } from '../vuex/getters'

    var table = window.table;
    var $ = window.jQuery;

    export default{
        data(){
            return{
//                empty_relation: {
//                    'relation':'',
//                    'model': '',
//                    'on_delete':'',
//                    'field':'',
//                    'editable':false,
//                    'type':'',
//                    'find': '',
//                    'model_obj':{'columns':[],'find_methods':[]},
//                    'pivot':0,
//                    'pivot_table':'',
//                    'pivot_self_key':'',
//                    'pivot_foreign_key':'',
//                    'pivot_columns': []
//                },
//                current_relation: {},

               // model: {},
               table,
               model_loaded:true,

            }
        },

        store,
        vuex: {
            getters: {
                model: getModel,
                config: getConfig
            },
            actions: {
                fetchModel:Actions.fetchModel,
                fetchConfig:Actions.fetchConfig
            }

        },

        created () {
            this.fetchConfig(this.table);
            this.fetchModel(this.table);


        },


        events: {

            delete_field(key) {
                this.deleteField(key);
            }
        },
        methods: {

            saveModel() {
                var form = $('form.validate').first();
                if (form.length) {
                    var bootstrapValidator = form.data('bootstrapValidator');
                    if (bootstrapValidator ) {
                        bootstrapValidator.validate();
                        if (!bootstrapValidator.isValid()) {
                            return false;
                        }
                    }
                }

                console.log(JSON.stringify(this.model));
            },

            deleteField(key) {

                if (confirm('Delete '+key+'?'))
                {
                    Vue.delete(this.model.fields,key);
                }

            },
        }

    }
</script>