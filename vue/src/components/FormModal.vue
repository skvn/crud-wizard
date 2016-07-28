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

                        <div class="col-md-5">
                            <label>Add tab</label>
                            <div class="input-group">
                            <input type="text"  class="form-control"  v-model="newTabTitle" placeholder="Tab title" >
                                <span class="input-group-btn">
                                <button class=" btn btn-primary" @click.prevent="addTab()">Ok</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-5  col-md-offset-1 form-group">
                            <label>Form Alias *</label>
                            <input type="text" name="key" class="form-control" :value="form_key" v-model="form_key" placeholder="Form alias *"
                                   required>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Form fields</h5>
                            <div style="max-height:450px;overflow:auto">
                            <div style="min-height:30px;background-color:lightgrey; margin-bottom: 20px;"  data-rel="fields_used"    class="list-group">

                                <template v-if="form.fields.length">
                                    <span  v-for="f  in  form.fields" style="cursor: move"
                                               data-rel="{{ f }}" class="list-group-item list-group-item-info">
                                        <b>{{ f }}</b> {{ model.fields[f].title }} ({{ model.fields[f].type }})
                                    </span>
                                </template>
                                <template v-else >
                                    <template v-for="(key, tab) in form.tabs">
                                        <span  class="list-group-item list-group-item-danger"
                                              data-view="{{ tab.view }}"
                                              data-key="{{ key }}"
                                              data-form_tab="1" style="cursor: move">
                                            <span>
                                                <span class="fa fa-folder-o"></span> {{ tab.title }}
                                             </span>
                                            <small v-if="tab.view">({{ tab.view }})</small>
                                            <div class="pull-right"><a href="#" @click.prevent="$dispatch('dom::remove_parent', $event)"  data-parent="span.list-group-item-danger" data-confirm="Really delete this tab?" class="label label-danger"><i class="fa fa-trash-o"></i> Remove</a>
                                            </div>
                                        </span>
                                        <template v-if="tab.fields.length">
                                            <span  v-for="f  in  tab.fields" style="cursor: move"
                                                   data-rel="{{ f }}" class="list-group-item list-group-item-info">
                                                <b>{{ f }}</b> {{ model.fields[f].title }} ({{ model.fields[f].type }})
                                            </span>
                                        </template>
                                    </template>
                                </template>

                                    <!--<template v-if="$parent.getFormType(form) == 'tabbed'">-->
                                        <!--<template v-for="(key, tab) in form">-->
                                            <!---->
                                        <!--</template>-->
                                    <!--</template>-->

                                <!--<template v-if="$parent.getFormType(form) == 'simple'">-->
                                        <!--<span  v-for="f  in  form" style="cursor: move"-->
                                               <!--data-rel="{{ f }}"-->
                                               <!--class="list-group-item list-group-item-info"><b>{{ f }}</b>-->
                                        <!--</span>-->

                                <!--</template>-->

                            </div>

                            </div>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <h5>Available fields (drag'n'drop to form fields)</h5>
                            <div style="max-height:450px;overflow:auto">
                            <div style="min-height:30px;"  data-rel="fields_stack"   class="list-group">
                                 <span  v-for="f  in  availableFields" style="cursor: move"
                                        data-rel="{{ f }}" class="list-group-item list-group-item-info">
                                        <b>{{ f }}</b> {{ model.fields[f].title }} ({{ model.fields[f].type }})
                                    </span>
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
                availableFields:[],
                usedFields: [],
                form: {fields:[], tabs:{}},
                form_key: "",
                edit:false,
                newTabTitle: "",
                fieldsContainer:null,
                fieldsStackContainer:null


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
                this.initEditForm(this.model.forms[key]);
                this.$broadcast('show::modal','form_modal');
                this.initDnd();
            },


        },

        methods: {


            addTab() {

                if (this.newTabTitle == '') {
                    swal('Oh no : (', 'Please, enter the tab title', 'warning');
                    return;
                }

                if (Object.keys(this.form.tabs).length == 0)
                {
                    //first tab
                    var form = {tabs:{'tab_0':{'title':this.newTabTitle, fields:this.form.fields}}, 'fields':[]};
                    this.$set('form', form);
                } else {
                    Vue.set(this.form.tabs,'tab_'+Object.keys(this.form.tabs).length,{'title':this.newTabTitle, fields:[]});
                }

//
//                this.form.tabs['tab_'+Object.keys(this.form.tabs).length] = {title:};
                this.newTabTitle = '';

            },

            initAvailFields() {

                var availableFields = [];

                if (Object.keys(this.model.fields).length) {
                    for (let i in this.model.fields) {
//                        console.log(i);
//                        console.log(this.usedFields.indexOf(i));
                        if (this.usedFields.indexOf(i) < 0) {
                            availableFields.push(i);
                        }
                    }
                }

                this.$set('availableFields', availableFields);
            },

            initEditForm(form) {

                console.log(this.$parent.getFormType(form));
                if (this.$parent.getFormType(form) == 'simple') {
                    this.form.fields = Object.assign({}, form);

                } else if (this.$parent.getFormType(form) == 'tabbed') {
                    this.form.tabs = Object.assign({}, form);
                    console.log(this.form.tabs);
                }

                this.usedFields = this.$parent.getFormFields(form);

            },
            initEmptyForm() {

                this.form = {fields: [], tabs: {}}

                if (typeof  this.model.forms == 'undefined' || !Object.keys(this.model.forms).length) {
                    this.form_key = "default";
                } else {
                    this.form_key = "form_" + (Object.keys(this.model.forms).length + 1);
                }

                this.edit = false;


            },


            hide() {

                this.initEmptyForm()
                this.$broadcast('hide::modal', 'form_modal')
            },

            save() {

                Actions.validateForm($('form#form_form'), () => {


//
//                    Vue.set(this.model.forms,this.form.key ,newForm);
//                    this.hide();

                });

            },

            initDnd () {

                this.initAvailFields();
                this.fieldsContainer = $('#form_form div[data-rel=fields_used]').first();
                this.fieldsStackContainer = $('#form_form div[data-rel=fields_stack]').first();

                var fc = this.fieldsContainer;
                var fs = this.availableFields;
                var form = this.form;
                var vm = this;
                this.fieldsStackContainer.sortable({
                    connectWith: this.fieldsContainer,
                    tolerance: 'pointer',
                    update: function (event, ui) {

                        vm.recollectUsedFields();

                        var trgt = ui.item.parents('div').first();
                        if (trgt.data('rel') == 'fields_used')
                        {
                            //in

                            ui.item.remove();

                        } else {
                            //out
                            if (fs.indexOf(ui.item.data('rel'))<0) {
                                fs.push(ui.item.data('rel'));
                            }
                        }


                    }
                }).disableSelection();

                this.fieldsContainer.sortable({
                    connectWith: this.fieldsStackContainer,
                    tolerance: 'pointer',
                    update: function (event, ui) {


                    }

                }).disableSelection();


            },

            recollectUsedFields() {

                var  tabs_el = this.fieldsContainer.find('*[data-form_tab]');

                if (tabs_el.length) {
                    var tab_index = 0;
                    var fields = {0: []};
                    //console.log(tabs_el.get(0));
                    var first_tab = $(tabs_el.get(0));
                    var tabs = [getTabData(first_tab, 0)];
                    first_tab.hide();

                    this.fieldsContainer.children(':visible').each(function (i) {


                        if ($(this).data('form_tab')) {


                            tab_index++;
                            tabs.push(getTabData($(this), tab_index));
                            fields[tab_index] = [];


                        } else {
                            fields[tab_index].push($(this).data('rel'));
                        }
                    });

                    first_tab.show();

                    var set_tabs = {};

                    for (let i=0; i<tabs.length; i++)
                    {
                        set_tabs[tabs[i].key] = Object.assign({},tabs[i]);
                        set_tabs[tabs[i].key].fields = fields[i];
                        delete set_tabs[tabs[i].key].key;
                    }

                    this.$set('form',{fields:[], tabs:set_tabs} );



//
//                    var tab = $cont.children('.list-group-item[data-form_tab]').get(0);
//
//
//                    var tab_info = getTabData(tab);
//                    this.form.tabs[key] = tab_info;
//                    this.form.tabs[key]['fields']
//                    tab.remove();
//
//                    var next_tab = $cont.children('.list-group-item[data-form_tab]').get(0);
//
//                    tabs = this.fieldsContainer.find('*[data-form_tab]');
//
//                    tabs.each(function (i) {
//
//
//                        var tab_fields = $(this).nextUntil('*[data-form_tab]','span');
//                        if (tab_fields.length)
//                        {
//                            tab.fields =
//                        } else {
//                            tab.fields = [];
//                        }
//                        newForm[key] = tab;
//                        i++;
//                    });
//
//                } else {
//
//                    newForm = [];
//                    $cont.find('*[data-rel]').each (function (i) {
//                        newForm.push($(this).data('rel'));
//                    });
//
//                }


                } else {
                    var newForm = [];
                    this.fieldsContainer.find('*[data-rel]').each (function (i) {
                        newForm.push($(this).data('rel'));
                    });
                    this.$set('form',{fields:newForm, tabs:{}} );
                }

                function getTabData(elem, i) {
                    var tab = {};
                    tab.title = $.trim(elem.find('span').text());
                    if (elem.data('view')) {
                        tab.view = elem.data('view');
                    }
                    var key = 'tab_' + i;
                    if (elem.data('key')) {
                        key = elem.data('key');
                    }
                    tab.key = key;

                    return tab;
                }

            }
        },


        watch: {

        }

    }
</script>
