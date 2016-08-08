<template>
    <!-- modal -->
    <modal id="relation_modal" size="lg" :fade="true">
        <div slot="modal-header">
            <template v-if="edit">Edit relation "{{ relation.title }}"</template>
            <template v-if="!edit">Add new  relation</template>
        </div>
        <div slot="modal-body">
            <form id="relation_form">
            <div class="card">
                <span class="label label-primary pull-right">{{ relation.relation }}</span>
                <br clear="all" />

                <div style="padding:10px;">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Title *</label>
                            <input type="text" name="title" class="form-control" :value="relation.title" v-model="relation.title" placeholder="Title *"
                                   required>

                        </div>
                        <div class="col-md-6  form-group">
                            <label>Attribute name (snake_case)*</label>
                            <input type="text" name="key" class="form-control" v-model="relation.key"  :value="relation.key"
                                   placeholder="Attribute name (snake_case)*" required :readonly="edit">
                        </div>

                        <div class="col-md-6  form-group">
                            <label>Model</label>
                            <select class="form-control default_select" name="model"   v-model="relation.model" required>
                                <option value="">Choose relation model</option>
                                <option v-for="m in config.all_models" v-bind:value="m">
                                    {{ m }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6  form-group" v-if="relation.relation=='belongsTo' || relation.relation=='hasFile'">
                            <label>Field (local key)
                                <select class="form-control default_select" name="field" v-model="relation.field"  :value="relation.field" required >
                                    <option value="">Choose Local key</option>
                                    <option v-for="(key, col) in config.table_int_columns" v-bind:value="col">
                                        {{ col }}
                                    </option>
                                </select>
                            </label>
                        </div>

                        <div class="col-md-6  form-group" v-if="relation.relation=='hasMany' || relation.relation=='hasOne'">
                            <label>Foreign key</label>
                            <select class="form-control default_select"
                                    v-model="relation.field" name="field" required  :value="relation.field">
                                <option value="">Choose relation model first</option>
                                <option v-for="col in model_obj.columns" v-bind:value="col">
                                    {{ col }}
                                </option>
                            </select>

                            <br/>
                            <label>On delete</label>
                            <select class="form-control default_select" name="on_delete" v-model="relation.on_delete">
                                <option v-for="(value, text) in config.on_delete_actions" v-bind:value="value">
                                    {{ text }}
                                </option>

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12" v-if="relation.relation=='belongsToMany'">
                        <div class="checkbox"><label><input type="radio"   v-model="relation.pivot" v-bind:value="0" /><b>Generate pivot table</b></label></div>
                        <div class="checkbox"><label><input type="radio"  v-model="relation.pivot" v-bind:value="1"   /><b>Choose existing pivot table</b></label></div>
                        <div class="form-group" v-if="relation.pivot == 1">
                            <select class="form-control" name="pivot_table"  v-model="relation.pivot_table" required >
                                <option value="">Choose Pivot table</option>
                                <option v-for="table in config.pivot_tables" v-bind:value="table">
                                    {{ table }}
                                </option>
                            </select>
                            <label> Self key:<br />
                                <select class="form-control" name="pivot_self_key" v-model="relation.pivot_self_key" required   >
                                    <option value="">Choose pivot self key</option>
                                    <option v-for="col in pivot_columns" v-bind:value="col">
                                        {{ col }}
                                    </option>
                                </select>
                            </label>
                            <label> Foreign  key:<br />
                                <select class="form-control" name="pivot_foreign_key" v-model="relation.pivot_foreign_key" required  >
                                    <option value="">Choose pivot foreign key</option>
                                    <option v-for="col in pivot_columns" v-bind:value="col">
                                        {{ col }}
                                    </option>
                                </select>
                            </label>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"  v-model="relation.editable"> Editable
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row"  v-show="relation.editable">

                        <div class="col-md-6">
                            <select v-model="relation.type" class="form-control default_select">
                                <option value="">Choose field type</option>
                                <option v-for="(value, text) in edit_types" v-bind:value="value">
                                    {{ text }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Data provider
                                method
                                == help('Config/Relations#options_providers')== </label>
                            <select v-model="relation.find">
                                <option value="">No method</option>
                                <option v-for="row in model_obj.find_methods" v-bind:value="row.name">
                                    {{ row.name }} ({{ row.description }})
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" v-model="relation.required">
                                    Required
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </form>
        </div>
        <div slot="modal-footer">
            <button class="btn btn-success" @click="save()">
                Save
            </button>
            <button class="btn btn-default" v-on:click="$broadcast('hide::modal', 'relation_modal')">
                Cancel
            </button>
        </div>
    </modal>



</template>

<script>

    import Vue from 'vue';
    import Modal from './ui/Modal.vue'
    import { getConfig, getModel } from '../vuex/getters'
    import Actions from '../vuex/actions'

    export default {

      name: 'RelationEdit',

      components: {
          Modal
        },

        vuex: {
            getters: {
                config: getConfig,
                model: getModel
            }

        },

      data(){
            return{
                empty_relation: {
                    'title':'',
                    'relation':'',
                    'model': '',
                    'on_delete':'',
                    'field':'',
                    'editable':false,
                    'type':'',
                    'find': '',
                    'pivot':0,
                    'pivot_table':'',
                    'pivot_self_key':'',
                    'pivot_foreign_key':'',
                },
                model_obj:{'columns':[],'find_methods':[]},
                relation: {},
                edit_types: {},
                pivot_columns: [],
                edit: false

            }
        },

        events: {
            // control modal from outside via events

            'relation::new'(type) {
                this.initEmptyRelation()
                this.relation.relation = type;
            },

            'relation::edit'(key) {
                this.edit = true;
                this.relation = this.model.fields[key];
                this.relation.key = key;
            },

            'hide::modal'(id) {
                if (id === 'relation_modal') {
                    this.initEmptyRelation()
                }
            }
        },

        methods: {
            initEmptyRelation() {
                this.relation =  Object.assign({},this.empty_relation);
                this.edit = false;
            },

            save() {

                Actions.validateForm($('form#relation_form'), () =>  {

                    let  relation = Object.assign({}, this.relation);

                    if (relation.find == "")
                    {
                        delete relation.find;
                    }

                    if (relation.on_delete == "")
                    {
                        delete relation.on_delete;
                    }
                    if (relation.required === false)
                    {
                        delete relation.required;
                    }
                    if (relation.relation != 'belongsToMany') {
                        delete relation.pivot;
                        delete relation.pivot_foreign_key;
                        delete relation.pivot_self_key;
                        delete relation.pivot_table;

                    }

                    Vue.set(this.model.fields,relation.key, relation);
                    this.$broadcast('hide::modal','relation_modal');
                    if (!this.edit) {
                        swal(
                                'Well done!',
                                'You\'ve just added a new relation',
                                'success'
                        );
                    }
                });

            },

        },

        watch: {
            'relation.relation': function (value) {
                var page_url = Actions.apiUrl +'getAvailableRelationFieldTypes'
                this.$http.get(page_url,{params:{args:[value]}}).then((resp)=>{
                   this.$set('edit_types',resp.json())
                });

            },
            'relation.model': function (value) {
                if (value) {
                    var page_url = Actions.apiUrl +'getRelationModelData'
                    this.$http.get(page_url,{params:{args:[value]}}).then((resp)=>{
                        this.$set('model_obj',resp.json())
                    });

                }
            },
            'relation.pivot_table': function (value) {
                if (value) {
                    var page_url = Actions.apiUrl +'getTableColumns'
                    this.$http.get(page_url,{params:{args:[value]}}).then((resp)=>{
                        this.$set('pivot_columns',resp.json())
                    });

                }
            }
        }

    }
</script>
