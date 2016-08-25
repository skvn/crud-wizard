<template>
  <!-- modal -->
  <modal id="field_modal" size="lg" :fade="true">
    <div slot="modal-header">
      <template v-if="edit">Edit field "{{ field.title }}"</template>
      <template v-if="!edit">Add new field</template>
    </div>
    <div slot="modal-body">
      <form id="field_form" v-on:submit="save($event); return false;">
        <div class="card">
          <span class="label label-primary pull-right">{{ field.type }}</span>
          <template v-if="!clone">
            <span class="label label-warning pull-right">{{ field.key }}</span>
          </template>
          <br clear="all"/>
          <div class="row">
            <div class="col-md-12" v-if="clone">
              <div class="col-md-8">
                <div class="form-group">

                  <label>Field key</label><br/>

                  <select class="form-control default_select" id="clone_key" style="display:inline; width:250px;"
                          v-model="field.key">
                    <option value="">Choose existing database table field</option>
                    <option v-for="f in config.table_columns | filterBy notUsedField" v-bind:value="f">
                      {{ f }}
                    </option>
                  </select>
                  or add a new one <input type="text" style="display:inline; width:150px;" v-model="key_new"/>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Title *</label>
                  <input type="text" class="form-control" name="title" :value="field.title"
                         required placeholder="Title of the field" v-model="field.title">

                </div>

                <div class="form-group" v-if="showField('date_format')">
                  <label>Format</label>
                  <select class="form-control default_select" name="format" required v-model="field.format">
                    <option value="">Choose format</option>
                    <option v-for="(key,v) in config.date_formats" v-bind:value="v.php">
                      {{ v.php }}
                    </option>
                  </select>
                </div>

                <div class="form-group" v-if="showField('date_time_format')">
                  <label>Format</label>
                  <select class="form-control default_select" name="format" required v-model="field.format">
                    <option value="">Choose format</option>
                    <option v-for="(key,v) in config.date_time_formats" v-bind:value="v.php">
                      {{ v.php }}
                    </option>
                  </select>
                </div>

                <div class="form-group" v-if="showField('height')">
                  <label>Height</label>
                  <input type="number" :value="field.height" step="1" name="height" style="width: 50px;"
                         v-model="field.height">
                </div>

                <div class="form-group" v-if="showField('editor_type')">
                  <label> Editor type</label>
                  <select class="form-control  default_select" name="editor_type" v-model="field.editor_type">
                    <option v-for="(value, text) in config.editor_types" v-bind:value="value">
                      {{ text }}
                    </option>
                  </select>
                </div>

                <div class="form-group" v-if="showField('range')">
                  <label> Start field</label>
                  <select class="form-control default_select" :value="field.fields[0]" v-model="field.fields[0]"
                          name="start_field" required>
                    <option value="">Choose field</option>
                    <option v-for="f in config.table_columns | filterBy notUsedField" v-bind:value="f">
                      {{ f }}
                    </option>
                  </select>
                  <label> End field</label>
                  <select class="form-control default_select" :value="field.fields[1]" v-model="field.fields[1]"
                          name="end_field" required>
                    <option value="">Choose field</option>
                    <option v-for="f in config.table_columns | filterBy notUsedField" v-bind:value="f">
                      {{ f }}
                    </option>
                  </select>
                </div>

                <div class="form-group" v-if="showField('number')">
                  <div class="row">
                    <div class="col col-xs-4">
                      <label>Min</label>
                      <input type="text" class="form-control" name="min" v-model="field.min">
                    </div>
                    <div class="col col-xs-4">
                      <label>Max</label>
                      <input type="text" class="form-control" name="max" v-model="field.max">
                    </div>
                    <div class="col col-xs-4">
                      <label>Step</label>
                      <input type="text" class="form-control" name="step" v-model="field.step">
                    </div>
                  </div>
                </div>

                <div class="form-group" v-if="showField('data_provider')">
                  <label> Data provider method *</label>
                  <select class="form-control default_select" name="find" :value="field.find" required
                          v-model="field.find">
                    <option value="">Choose method</option>
                    <option v-for=" f in config.find_methods" v-bind:value="f.name">
                      {{ f.name }} ({{ f.description }})
                    </option>
                  </select>


                </div>


              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label> Hint</label>
                  <input type="text" class="form-control"
                         name="hint" :value="field.hint" placeholder="a little hint for the user"
                         v-model="field.hint">
                </div>
                <div class="form-group">
                  <label> Extra attributes</label>
                  <input type="text" class="form-control" name="extra" :value="field.extra"
                         placeholder="disabled, readonly. etc"
                         v-model="field.extra"
                  >
                </div>
                <div class="form-group">
                  <div class="checkbox" v-if="showField('required')">
                    <label>
                      <input type="checkbox" name="required" v-model="field.required"/> Required
                    </label>
                  </div>
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
      <button class="btn btn-default" v-on:click="$broadcast('hide::modal', 'field_modal')">
        Cancel
      </button>
    </div>
  </modal>


</template>

<script>

  import Vue from 'vue';
  import Modal from './ui/Modal.vue'
  import {getConfig, getModel} from '../vuex/getters'
  import Actions from '../vuex/actions'

  export default {

    name: 'FieldEdit',

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
      return {
        empty_field: {},

        field: {
          type: '',
          key: ''
        },
        edit: false,
        clone: false,
        key_new: '',

        field_config: {}

      }
    },

    events: {
      // control modal from outside via events

      'field::new'({type, key}) {
        this.initEmptyField()
        this.field.key = key;
        this.field.type = type;
        this.initFieldConfig(true);
      },

      'field::edit'(key) {
        this.edit = true;
        this.field = JSON.parse(JSON.stringify(this.model.fields[key]));
        this.field.key = key;
        this.initFieldConfig();
      },

      'field::clone'(key) {
        this.edit = false;
        this.clone = true;
        this.field = JSON.parse(JSON.stringify(this.model.fields[key]));
        this.field.key = '';
        this.initFieldConfig();
      },

      'hide::modal'(id) {
        if (id === 'field_modal') {
          this.initEmptyField()
        }
      }
    },

    methods: {
      initEmptyField() {
        this.field = Object.assign({}, this.empty_field);
        this.edit = false;
      },


      save(e) {
        console.log(e);
        if (e) {
          e.preventDefault();
        }

        if (this.clone && this.key_new != '') {
          this.field.key = this.key_new
        }

        if (this.field.key == '') {
          swal('Please choose field key', '', 'error');
          $('#clone_key').focus();
          return false;

        }

        Actions.validateForm($('form#field_form'), () => {
          delete this.field.is_for_virtual;
          var key = this.field.key;
          delete this.field.key;
          Vue.set(this.model.fields, key, Object.assign({}, this.field));
          this.initEmptyField();
          this.$broadcast('hide::modal', 'field_modal');
        });

      },

      initFieldConfig(assign_defaults) {

        this.$set('field_config', this.config.fields_config[this.field.type]);
        if (assign_defaults) {
          Object.assign(this.field, this.field_config.defaults);
        }
      },

      isCustomField() {
        return this.field_config.custom;
      },

      showField(field_name) {

        console.log(this.field_config['sections']);

        if (typeof this.field_config['sections'] == 'object'
                && this.field_config['sections'].indexOf(field_name) >= 0) {
          return true;
        }

        return false;
      },

      notUsedField(row) {

        if (typeof this.model.fields[row] == 'undefined') {
          return true;
        }

        return false;
      },

    },

    watch: {

      'key_new': function (val, oldVal) {
        if (val != '') {
          this.field.key = "";
        }
      },

      'field.key': function (val, oldVal) {
        if (val != '') {
          this.key_new = "";
        }
      },

    }

  }
</script>
