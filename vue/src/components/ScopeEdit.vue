<template>
    <!-- modal -->
    <modal id="scope_modal" size="lg" :fade="true">
        <div slot="modal-header">
            <h3 v-if="edit">Edit scope "{{ scopeKey }}"</h3>
            <h3 v-if="!edit">Add new scope</h3>
        </div>
        <div slot="modal-body">
            <form id="scope_form">
            <div class="card">
                <div style="padding:10px;">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label>Scope title</label>
                                <input type="text" class="form-control" placeholder="New scope"  name="title" :value="scope.title" v-model="scope.title"  required />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Scope alias</label>
                                    <input type="text" class="form-control" placeholder="new_scope"  name="alias" :value="scopeKey" v-model="scopeKey"  required />
                                </div>
                            </div>

                            <div class="col-lg-4">

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="scope.allow_add" />
                                        <b>Allow add new items</b>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="scope.multiselect"  />
                                        <b>Allow multi select</b>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="scope.edit_tab" />
                                        <b>Use tabs for edit interface (otherwise a popup would be used)</b>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="scope.buttons.single_edit" />
                                        <b>Show "edit" button for each row</b>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="scope.buttons.single_delete" />
                                        <b>Show "delete" button for each row</b>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="scope.buttons.mass_delete" />
                                        <b>Show "Bulk delete" button</b>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="1"  v-model="scope.buttons.customize_columns"  />
                                        <b>Allow show/hide columns</b>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <b>Default sort options</b>
                                <li v-for="(f, o) in scope.sort">
                                    {{ f }} = {{ o }} <a href="#" class="text-danger" @click.prevent="removeSortOption(f)"><i class="fa fa-times-circle"></i></a>
                                </li>
                                <a href="#" class="btn btn-primary"  data-click="crud_action"  data-action="wizard_add_list_col" data-skip_arr="1" data-fragment="sort_tpl_{{ ALIAS }}" data-container="list_sorts_{{ ALIAS }}"><i class="fa fa-plus"></i> Add sort option</a>

                                <hr />
                                <div data-rel="is_tree" v-if="model.tree">
                                <label>
                                    List type
                                    <select name="list[{{ ALIAS }}][list_type]"  class="form-control default_select" style="width:250px;">
                                        {% for op_val, op_text in wizard.getAvailableTreeLists() %}
                                        <option value="{{ op_val }}" {{ list.list_type==op_val?'selected' }}>{{ op_text }}</option>
                                        {% endfor %}
                                    </select>
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
            <button class="btn btn-default" v-on:click="hide()">
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

      name: 'ScopeEdit',

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
                scopeKey: "",
                edit:false,
                scope: {},
                newScope: {
                    title : "",
                    multiselect: true,
                    buttons: {
                        add_new: true,
                        single_edit: true,
                        single_delete: true,
                        mass_delete: true,
                        customize_columns: false
                    }
                },
            }
        },

        events: {

            'scope::new'() {

                this.initEmptyScope();
                this.$broadcast('show::modal','scope_modal');
            },

            'scope::edit'(key) {
                this.edit = true;
                this.scopeKey = key;
                this.$broadcast('show::modal','scope_modal');
                this.initEditScope(this.model.scopes[key]);
            },


        },

        created() {
            this.initEmptyScope()
        },

        methods: {


            initEditScope(scope) {

                this.$set('scope',scope);
            },

            initEmptyScope() {

                if (this.model && this.model.scopes && Array.isArray(this.model.scopes)) {
                    this.scopeKey = 'scope_' + Object.keys(this.model.scopes).length;
                }
                this.$set('scope',this.newScope);
            },


            hide() {

                this.$broadcast('hide::modal', 'scope_modal')
            },

            save() {

                Actions.validateForm($('form#scope_form'), () => {


                    this.hide();

                });

            },

        },


        watch: {

        }

    }
</script>
