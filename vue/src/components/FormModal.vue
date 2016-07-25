<template>
    <!-- modal -->
    <modal id="form_modal" size="lg" :fade="true">
        <div slot="modal-header">
            <h3 v-if="edit">Edit form "{{ form.name }}"</h3>
            <h3 v-if="!edit">Add new  form</h3>
        </div>
        <div slot="modal-body">
            <form id="form_form">
            <div class="card">
                <div style="padding:10px;">
                    <div class="row">

                        <div class="col-md-6">
                            <label>Add tab</label>
                            <div class="input-group">
                            <input type="text" name="key" class="form-control"  v-model="newTabTitle" placeholder="Tab title"
                            >
                                <span class="input-group-btn">
                                <button class=" btn btn-primary" @click.prevent="addTab()">Ok</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Form Alias *</label>
                            <input type="text" name="key" class="form-control" :value="form.key" v-model="form.key" placeholder="Form alias *"
                                   required>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Form fields</h5>
                            <div style="max-height:450px;overflow:auto">
                            <div style="min-height:30px;background-color:lightgrey; margin-bottom: 20px;"  data-rel="fields_used"    class="list-group">
                            </div>

                            </div>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <h5>Available fields (drag'n'drop to form fields)</h5>
                            <div style="max-height:450px;overflow:auto">
                            <div  data-rel="fields_stack"   class="list-group">
                                 <span  v-for="(f, fld)  in availableFields" style="cursor: move"
                                        data-rel="{{ f }}"
                                        class="list-group-item list-group-item-info"><b>{{ f }}</b> {{ fld.relation ? 'Relation ' :'' }}{{ fld.title }} ({{ fld.type }})</span>

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
            <button class="btn btn-default" v-on:click="$broadcast('hide::modal', 'form_modal')">
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

      name: 'FormModal',

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
                form: {},
                edit:false,
                newTabTitle: "",


            }
        },

        computed: {
            usedFields: function () {
                //let available_keys = this;
            },

            availableFields: function () {

                return this.model.fields;
            }
        },
        events: {
            // control modal from outside via events

            'form::new'(type) {
                this.initEmptyForm();
                this.$broadcast('show::modal','form_modal');
                this.initDnd();

            },

            'form::edit'(key) {
                this.edit = true;
                this.initDnd();
            },

            'hide::modal'(id) {
                if (id === 'form_modal') {
                    this.initEmptyForm()
                }
            }
        },

        methods: {

            addTab() {

                if (this.newTabTitle == '')
                {
                    swal('Oh no : (','Please, enter the tab title','warning');
                    return;
                }

                let $cont = $('#form_form div[data-rel=fields_used]').first();
                $('<span class="list-group-item list-group-item-danger" data-form_tab="1"><span><span class="fa fa-folder-o"></span> '+this.newTabTitle+'</span> <div class="pull-right"><a href="#"  data-parent="span.list-group-item-danger" class="label label-danger"><i class="fa fa-trash-o"></i> Remove</a></div></span>').appendTo($cont);
            },
            initEmptyForm() {
                this.form = new Object();
                this.usedFields = [];


                if (typeof  this.model.forms == 'undefined' || !Object.keys(this.model.forms).length)
                {
                    this.form.key = "default";
                } else {
                    this.form.key = "form_"+(Object.keys(this.model.forms).length+1);
                }

                this.edit = false;
            },

            save() {

                Actions.validateForm($('form#form_form'), () =>  {});

            },

            initDnd () {


                $('#form_form div[data-rel=fields_stack]').each (function () {

                    $(this).sortable({
                        connectWith: $('#form_form div[data-rel=fields_used]'),
                        tolerance: 'pointer',
                        update: function (event, ui) {
                            //update_form_stubs();
                        }
                    }).disableSelection();

                    $('#form_form div[data-rel=fields_used]').sortable({
                        connectWith: $(this),
                        tolerance: 'pointer'
                    }).disableSelection();

                });

            },

        },


        watch: {

        }

    }
</script>
