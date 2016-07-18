<template>
    <!-- modal -->
    <modal id="relation_modal" size="lg" :fade="true">
        <div slot="modal-header">
            <h3>header</h3>
        </div>
        <div slot="modal-body">
            <div class="card">
                <span class="label label-primary pull-right">{{ relation.relation }}</span>
                <br clear="all" />

                <div style="padding:10px;">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Title *</label>
                            <input type="text" class="form-control" v-model="relation.title" placeholder="Title *"
                                   required>

                        </div>
                        <div class="col-md-6">
                            <label>Attribute name (snake_case)*</label>
                            <input type="text" class="form-control" v-model="relation.key"
                                   placeholder="Attribute name (snake_case)*" required :readonly="edit">

                        </div>

                        <div class="col-md-6">
                            <label>Model</label>
                            <select class="form-control default_select" v-model="relation.model" required>
                                <option value="">Choose relation model</option>
                                <option v-for="m in config.all_models" v-bind:value="m">
                                    {{ m }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6" v-if="relation.relation=='belongsTo'">
                            <label>Field (local key)
                                <select class="form-control default_select" v-model="relation.field" required >
                                    <option value="">Choose Local key</option>
                                    {% for f in wizard.getTableColumns(table) %}
                                    {% if f != 'id' %}
                                    <option value="{{ f }}">{{ f }}</option>
                                    {% endif %}
                                    {% endfor %}
                                </select>
                            </label>
                        </div>

                        <div class="col-md-6" v-if="relation.relation=='hasMany' || relation.relation=='hasOne'">
                            <label>Foreign key</label>
                            <select class="form-control default_select"
                                    v-model="relation.field" required>
                                <option value="">Choose relation model first</option>
                                <option v-for="col in relation.model_obj.columns" v-bind:value="col">
                                    {{ col }}
                                </option>
                            </select>

                            <br/>
                            <label>On delete</label>
                            <select class="form-control default_select" v-model="relation.on_delete">
                                {% for av, atext in wizard.getOndeleteActions() %}
                                <option value="{{ av }}">{{ atext }}</option>
                                {% endfor %}

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12" v-if="relation.relation=='belongsToMany'">
                        <div class="checkbox"><label><input type="radio"   v-model="relation.pivot" v-bind:value="0" /><b>Generate pivot table</b></label></div>
                        <div class="checkbox"><label><input type="radio"  v-model="relation.pivot" v-bind:value="1"   /><b>Choose existing pivot table</b></label></div>
                        <div v-if="relation.pivot == 1">
                            <select class="form-control"  v-model="relation.pivot_table" required >
                                <option value="">Choose Pivot table</option>
                                {% for table in wizard.getPossiblePivotTables() %}
                                <option value="{{ table }}">{{ table }}</option>
                                {% endfor %}
                            </select>
                            <label> Self key:<br />
                                <select class="form-control" v-model="relation.pivot_self_key" required   >
                                    <option value="">Choose pivot self key</option>
                                    <option v-for="col in relation.pivot_columns" v-bind:value="col">
                                        {{ col }}
                                    </option>
                                </select>
                            </label>
                            <label> Foreign  key:<br />
                                <select class="form-control" v-model="relation.pivot_foreign_key" required  >
                                    <option value="">Choose pivot foreign key</option>
                                    <option v-for="col in relation.pivot_columns" v-bind:value="col">
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
                                method  wiz_macros.help('Config/Relations#options_providers') </label>
                            <select v-model="relation.find">
                                <option value="">No method</option>
                                <option v-for="row in relation.model_obj.find_methods" v-bind:value="row.name">
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

    import Modal from './ui/Modal.vue'
    import { getConfig } from '../vuex/getters'
    import Actions from '../vuex/actions'

    export default {

      name: 'RelationModal',

      components: {
          Modal
        },

        vuex: {
            getters: {
                config: getConfig
            }

        },

      data(){
            return{
                empty_relation: {
                    'relation':'',
                    'model': '',
                    'on_delete':'',
                    'field':'',
                    'editable':false,
                    'type':'',
                    'find': '',
                    'model_obj':{'columns':[],'find_methods':[]},
                    'pivot':0,
                    'pivot_table':'',
                    'pivot_self_key':'',
                    'pivot_foreign_key':'',
                    'pivot_columns': []
                },
                relation: {},
                edit_types: {},

            }
        },

        events: {
            // control modal from outside via events
            'relation::new'(type) {
                this.initEmptyRelation()
                this.relation.relation = type;
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
            },

            toJson() {

            }
        },

        watch: {
            'relation.relation': function (value) {
                var page_url = Actions.apiUrl +'getAvailableRelationEditTypes'
                this.$http.get(page_url,{params:{args:[value]}}).then((resp)=>{
                   this.$set('edit_types',resp.json())
                });

            },
            'relation.model': function (value) {
                if (value) {
                    //this.fillRelationModel(value, 'current_relation.model_obj');
                }
            },
            'relation.pivot_table': function (value) {
                if (value) {
                    //this.fillTableColumns(value, 'current_relation.pivot_columns');
                }
            }
        }

    }
</script>
