<template>
    <h1 v-show="!model_loaded" ><i  class="fa fa-spinner fa-spin"></i></h1>
    <div :class="{ 'container-fluid in ': model_loaded }" class="container-fluid fade">

        <div class="row">
            <div class="col-md-12">

                <!-- Nav tabs -->
                <div class="card">
                    <div class="pull-left" style="width: 90%">
                        <ul class="nav nav-pills">
                            <li role="presentation"><a  v-link="'general'">General settings</a></li>
                            <li role="presentation"><a  v-link="'relations'">Relations</a></li>
                            <li role="presentation"><a  v-link="'fields'">Fields</a></li>
                            <li role="presentation"><a  v-link="'forms'">Forms</a></li>
                            <li role="presentation"><a  v-link="'scopes'">Scopes</a></li>
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
            },

            'dom::remove_parent': function (event) {
                var trgt = $(event.target);
                var parent;
                if (trgt.data('parent')) {
                    parent = $(trgt.data('parent'))
                } else {
                    parent = trgt.parent();
                }

                if (trgt.data('confirm')){

                    swal(
                            {
                                title: trgt.data('confirm'),
                                text: "You will not be able to recover this element!",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes, delete it!"
                            })
                            .then(function () {

                                parent.remove();
                            }, function () {

                            });


                } else {
                    parent.remove();
                }
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

                var page_url = Actions.apiUrl +'saveModel'
                this.$http.post(page_url,{args:[this.table,JSON.stringify(this.model)]}).then((resp)=>{
                    console.log(resp.json());
                });
            },

            deleteField(key) {


                swal(
                        {
                            title: "Are you sure?",
                            text: "You will not be able to recover this field!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!"

                })
                .then(() => {

                    Vue.delete(this.model.fields, key);
                    swal(
                            'Deleted!',
                            'The field has been deleted.',
                            'success'
                    );
                }, () => {});


            },
        }

    }
</script>