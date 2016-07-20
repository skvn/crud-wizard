<template>
    <!-- modal -->
    <modal id="field_modal" size="lg" :fade="true">
        <div slot="modal-header">
            <h3 v-if="edit">Edit field "{{ relation.title }}"</h3>
            <h3 v-if="!edit">Add new  field</h3>
        </div>
        <div slot="modal-body">
            <form id="field_form">
            <div class="card">
                <span class="label label-primary pull-right">{{ field.type }}</span>
                <span class="label label-warning pull-right">{{ field.key }}</span>
                <br clear="all" />
                <div class="row" style="padding:10px;">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title *</label>
                                <input type="text"  class="form-control" name="title" :value="field.title"
                                       required placeholder="Title of the field" v-model="field.title" >

                        </div>

                        <div class="form-group"  v-if="showField('height')">
                        <label>Height</label>
                            <input type="number" :value="!field.height?500" step="1" name="height" style="width: 50px;" v-model="field.height">
                        </div>

                        <div class="form-group"  v-if="showField('editor_type')">
                            <label> Editor type</label>
                                <select class="form-control  default_select" name="editor_type" v-model="field.editor_type"  >
                                    <option v-for="(value, text) in config.editor_types"  v-bind:value="value">
                                        {{ text }}
                                    </option>
                                </select>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <label> Hint</label>
                        <input type="text" class="form-control"
                               name="hint" :value="field.hint" placeholder="a little hint for the user"
                               v-model="field.hint" >

                        <label> Extra attributes</label>
                        <input type="text" class="form-control" name="extra" :value="field.extra" placeholder="disabled, readonly. etc"
                               v-model="field.extra"
                        >

                        <div class="checkbox" v-if="showField('required')">
                            <label>
                                <input  type="checkbox" name="required" v-model="field.required" /> Required
                            </label>
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
    import { getConfig, getModel } from '../vuex/getters'
    import Actions from '../vuex/actions'

    export default {

      name: 'RelationModal',

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
                empty_field: {

                },

                field: {
                    type:'',
                    key: ''
                },
                edit: false

            }
        },

        events: {
            // control modal from outside via events

            'field::new'({type,key}) {
                this.initEmptyField()
                this.field.key = key;
                this.field.type = type;
                Object.assign(this.field, this.config.field_defaults[type]);
            },

            'field::edit'(key) {
                this.edit = true;
                this.field = this.model.fields[key];
                this.field.key = key;
            },

            'hide::modal'(id) {
                if (id === 'field_modal') {
                    this.initEmptyField()
                }
            }
        },

        methods: {
            initEmptyField() {
                this.field =  Object.assign({},this.empty_field);
                this.edit = false;
            },

            save() {
                Actions.validateForm($('form#field_form'), () => {
                });

            },
            showField(field_name) {


                if (typeof this.config.field_section_config[this.field.type] == 'object' &&
                        this.config.field_section_config[this.field.type].indexOf(field_name)>=0)
                {
                    return true;
                }

                return false;
            }

        },

        watch: {

        }

    }
</script>
