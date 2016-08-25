<template>
  <!-- modal -->
  <modal id="scope_modal" size="lg" :fade="true">
    <div slot="modal-header">
      <template v-if="edit">Edit scope "{{ scopeKey }}"</template>
      <template v-if="!edit">Add new scope "{{ scopeKey }}"</template>
    </div>
    <div slot="modal-body">
      <form id="scope_form" v-on:submit.prevent="save()">
        <div class="card">
          <div style="padding:10px;">
            <!--<div class="row">-->
            <!--<div class="col-lg-6">-->
            <!---->
            <!--</div>-->

            <!--&lt;!&ndash;<div class="col-lg-6">&ndash;&gt;-->
            <!--&lt;!&ndash;<div class="form-group">&ndash;&gt;-->
            <!--&lt;!&ndash;<label>Scope alias</label>&ndash;&gt;-->
            <!--&lt;!&ndash;<input type="text" class="form-control" placeholder="new_scope"  name="alias" :value="scopeKey" v-model="scopeKey"  required />&ndash;&gt;-->
            <!--&lt;!&ndash;</div>&ndash;&gt;-->
            <!--&lt;!&ndash;</div>&ndash;&gt;-->

            <!--</div>-->
            <div class="row">
              <div class="col-lg-4">

                <div class="form-group">
                  <label>Scope title</label>
                  <input type="text" class="form-control" placeholder="New scope" name="title" :value="scope.title"
                         v-model="scope.title" required/>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" v-model="scope.allow_add"/>
                    <b>Allow add new items</b>
                  </label>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" v-model="scope.multiselect"/>
                    <b>Allow multi select</b>
                  </label>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" v-model="scope.edit_tab"/>
                    <b>Use tabs for edit interface (otherwise a popup would be used)</b>
                  </label>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" v-model="scope.buttons.single_edit"/>
                    <b>Show "edit" button for each row</b>
                  </label>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" v-model="scope.buttons.single_delete"/>
                    <b>Show "delete" button for each row</b>
                  </label>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" v-model="scope.buttons.mass_delete"/>
                    <b>Show "Bulk delete" button</b>
                  </label>
                </div>

                <div class="form-group">
                  <label class="checkbox-inline">
                    <input type="checkbox" value="1" v-model="scope.buttons.customize_columns"/>
                    <b>Allow show/hide columns</b>
                  </label>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="row" style="margin-right:0px;">
                  <div class="col-md-12 bg-success">
                    <b>Edit form</b>
                    <div class="clearfix"></div>
                    <div class="form-group">
                      <select class="form-control default_select" v-model="scope.form">
                        <option value="">Choose form</option>
                        <option v-for="(key, f) in model.forms" v-bind:value="key">
                          {{ key }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12" style="background-color:#f5f5f5; padding-right:0px;">
                    <b>Default sort options</b>
                    <div class="clearfix"></div>

                    <div class="col-md-6">
                      <li v-for="(f, o) in scope.sort">
                        {{ f }} &rarr; {{ o }} <a href="#" class="text-danger" @click.prevent="removeSortOption(f)"><i
                              class="fa fa-times-circle"></i></a>
                      </li>
                    </div>
                    <div class="col-md-6" style="padding-right:0px;">
                      <label>Field
                        <select class="form-control default_select" v-model="newSortField">
                          <option value="">Choose column</option>
                          <option v-for="(key, col) in config.table_columns" v-bind:value="col">
                            {{ col }}
                          </option>
                        </select>
                      </label>
                      <br/>
                      <label>Direction
                        <select class="form-control default_select" v-model="newSortDir">
                          <option value="asc">Ascending</option>
                          <option value="desc">Descending</option>
                        </select>
                      </label>
                      <br/>
                      <a href="#" class="btn btn-primary" @click.prevent="addSortOption()"><i class="fa fa-plus"></i>
                        Add</a>
                    </div>
                  </div>
                </div>


                <hr/>
                <div data-rel="is_tree" v-if="model.tree">
                  <label>
                    List type
                    <select name="list_type" class="form-control default_select" required>
                      <option v-for="(key, type) in config.tree_lists" v-bind:value="key">
                        {{ type }}
                      </option>
                    </select>
                  </label>
                </div>
              </div>
            </div>
            <hr>
            <h3>Columns</h3>


            <a href="#" class="btn btn-success pull-right" @click.prevent="addColumn()"><i class="fa fa-plus"></i> Add
              column</a>
            <br>
            <div class="col-md-12">
              <table class="table table-striped" id="list_cols">
                <tbody v-sortable="sortableColOptions">
                <template v-for="column in scope.list">
                  <list-column :column="column"></list-column>
                </template>
                </tbody>
              </table>
            </div>
            <a href="#" class="btn btn-success pull-right" @click.prevent="addColumn()"><i class="fa fa-plus"></i> Add
              column</a>

            <div class="clearfix"></div>
            <hr>
            <h3>List actions</h3>
            <div class="col-md-12 bg-info">
              <a href="#" class="btn btn-primary pull-right" @click.prevent="addAction();"><i class="fa fa-plus"></i>
                Add action</a>
              <table class="table table-striped" id="list_actions">
                <tbody v-sortable="sortableColOptions">
                <template v-for="(key, a) in scope.list_actions">
                  <list-action :action="a" :scope="scope" :key="key"></list-action>
                </template>
                </tbody>
              </table>
              <a href="#" class="btn btn-primary pull-right" @click.prevent="addAction();"><i class="fa fa-plus"></i>
                Add action</a>
            </div>
            <div class="clearfix"></div>


          </div>
        </div>
      </form>
    </div>
    <div slot="modal-footer">
      <button class="btn btn-success" @click="save()">
        Save
      </button>

      <button class="btn btn-default" v-on:click="hide()">
        Close
      </button>
    </div>
  </modal>


</template>

<script>

  import Vue from 'vue';
  import Modal from './ui/Modal.vue'
  import {getConfig, getModel} from '../vuex/getters'
  import Actions from '../vuex/actions'
  import ListColumn from './ScopeListColumn.vue'
  import ListAction from './ScopeListAction.vue'

  export default {

    name: 'ScopeEdit',

    components: {
      Modal,
      ListColumn,
      ListAction
    },

    vuex: {
      getters: {
        config: getConfig,
        model: getModel
      }

    },

    data(){
      return {
        scopeKey: "",
        edit: false,
        scope: {},
        newSortField: '',
        newSortDir: 'asc',
        newScope: {
          title: "",
          form: "",
          multiselect: true,
//                    list:[],
//                    list_actions:[],
          buttons: {
            add_new: true,
            single_edit: true,
            single_delete: true,
            mass_delete: true,
            customize_columns: false
          }
        },

        sortableColOptions: {
          handle: ".drag_cols",
          onUpdate: (evt) => {
            this.scope.list.move(evt.oldIndex, evt.newIndex);
          },
        }
      }
    },

    events: {

      'list_action::delete'(key) {

        this.deleteListAction(key);
      },

      'list_column::delete'(key) {

        this.deleteListColumn(key);
      },

      'scope::new'(alias) {

        this.initEmptyScope(alias);
        this.$broadcast('show::modal', 'scope_modal');
      },

      'scope::edit'(key) {
        this.edit = true;
        this.scopeKey = key;
        this.$broadcast('show::modal', 'scope_modal');
        this.initEditScope(key);

//                $('#list_cols').sortable({handle:".drag_cols",cursor: "move",axis: "y"});
//                console.log($('#list_cols'));
      },

    },

    created() {
      this.initEmptyScope()
    },


    methods: {

      addAction() {

        if (typeof  this.scope.list_actions == 'undefined') {
          this.$set('scope.list_actions', []);
        }

        this.scope.list_actions.push({title: 'New action',});
      },

      addColumn() {

        if (typeof  this.scope.list == 'undefined') {
          this.$set('scope.list', []);
        }

        this.scope.list.push({data: '', format: ''});
      },
      addSortOption() {

        if (this.newSortField == '') {
          swal('Choose field', '', 'warning');
          return false;
        }

        if (typeof this.scope.sort == 'undefined') {
          this.$set('scope.sort', {});
        }
        Vue.set(this.scope.sort, this.newSortField, this.newSortDir);

        this.newSortField = '';
        this.newSortDir = 'asc'
      },

      deleteListAction(key) {

        swal(
                {
                  title: "Are you sure?",
                  text: "You will not be able to recover this action!",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes, delete it!"
                })
                .then(() => {

                  this.scope.list_actions.splice(key, 1);

                }, () => {
                });

      },

      deleteListColumn(key) {

        swal(
                {
                  title: "Are you sure?",
                  text: "You will not be able to recover this column!",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes, delete it!"
                })
                .then(() => {

                  this.scope.list.splice(key, 1);

                }, () => {
                });

      },

      initEditScope(key) {

        this.$set('scope', JSON.parse(JSON.stringify(this.model.scopes[key])));
      },

      initEmptyScope(alias) {

//                if (this.model && this.model.scopes) {
//                    this.$set('scopeKey','scope_' + Object.keys(this.model.scopes).length);
//                } else {
//                    this.$set('scopeKey','default');
//                }
        if (alias) {
          this.$set('scopeKey', alias);
        }
        this.$set('scope', this.newScope);
      },


      hide() {

        this.$broadcast('hide::modal', 'scope_modal')
      },

      removeSortOption(f) {
        swal(
                {
                  title: "Are you sure?",
                  text: "",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes, delete it!"
                })
                .then(() => {

                  Vue.delete(this.scope.sort, f);

                }, () => {
                });

      },
      save() {

        Actions.validateForm($('form#scope_form'), () => {

          if (!Object.keys(this.model.scopes).length) {
            let scopes = {};
            scopes[this.scopeKey] = JSON.parse(JSON.stringify(this.scope));
            this.$set('model.scopes', scopes);
          } else {
            Vue.set(this.model.scopes, this.scopeKey, JSON.parse(JSON.stringify(this.scope)));
          }


          this.hide();

        });

      },

    },


    watch: {}

  }
</script>
