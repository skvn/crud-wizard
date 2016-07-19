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
                <span class="label label-info pull-right">{{ field.key }}</span>
                <br clear="all" />

                <div style="padding:10px;">

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

                field: {},
                edit: false

            }
        },

        events: {
            // control modal from outside via events

            'field::new'(type) {
                this.initEmptyField()
                //this.relation.relation = type;
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

        },

        watch: {
            
        }

    }
</script>
