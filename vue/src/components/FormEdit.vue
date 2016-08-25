<template>
    <!-- modal -->
    <modal id="form_modal" size="lg" :fade="true">
        <div slot="modal-header">
            <template v-if="edit">Edit form "{{ formKey }}"</template>
            <template v-if="!edit">Add new  form</template>
        </div>
        <div slot="modal-body">
            <form id="form_form">
            <div class="card">
                <div style="padding:10px;">
                    <div class="row">

                        <div class="col-md-5">
                            <label>Add tab</label>
                            <div class="input-group">
                            <input type="text"  class="form-control" @keyup.enter.prevent="addTab()" v-model="newTabTitle" placeholder="Tab title" >
                                <span class="input-group-btn">
                                <button class=" btn btn-primary" @click.prevent="addTab()">Ok</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-5  col-md-offset-1 form-group">
                            <label>Form Alias *</label>
                            <input type="text" name="key" class="form-control" :value="formKey" v-model="formKey" placeholder="Form alias *"
                                   required>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Form fields</h5>
                            <div style="max-height:450px;overflow:auto">
                            <div style="min-height:30px;background-color:lightgrey; margin-bottom: 20px;"
                                   class="list-group" v-sortable="sortableDestOptions">

                                <template v-for="(key, f) in fields">

                                    <template v-if="f.tab">
                                        <span  class="list-group-item  list-group-item-danger" style="cursor: move">
                                            <button class="btn btn-danger pull-right" @click.prevent="deleteField(key)"><i class="fa fa-trash-o"></i></button>
                                            <span>
                                            <span class="fa fa-folder-o"></span> {{ f.title }}
                                            </span>
                                            <small v-if="f.view">({{ f.view }})</small>
                                        </span>
                                    </template>
                                    <template v-else >
                                        <span  class="list-group-item" style="cursor: move">
                                            <button class="btn btn-danger pull-right" @click.prevent="deleteField(key, f.key)"><i class="fa fa-trash-o"></i></button>
                                            <b>{{ f.key }}</b> {{ model.fields[f.key].title }} ({{ model.fields[f.key].type }})

                                        </span>
                                    </template>
                                </template>


                                <!--<template v-if="form.fields.length">-->
                                    <!--<span  v-for="f  in  form.fields" style="cursor: move"-->
                                               <!--data-rel="{{ f }}" class="list-group-item list-group-item-info">-->
                                        <!--<b>{{ f }}</b> {{ model.fields[f].title }} ({{ model.fields[f].type }})-->
                                    <!--</span>-->
                                <!--</template>-->
                                <!--<template v-else >-->
                                    <!--<template v-for="(key, tab) in form.tabs">-->
                                        <!--<span  class="list-group-item list-group-item-danger"-->
                                              <!--data-view="{{ tab.view }}"-->
                                              <!--data-key="{{ key }}"-->
                                              <!--data-form_tab="1" style="cursor: move">-->
                                            <!--<span>-->
                                                <!--<span class="fa fa-folder-o"></span> {{ tab.title }}-->
                                             <!--</span>-->
                                            <!--<small v-if="tab.view">({{ tab.view }})</small>-->
                                            <!--<div class="pull-right"><a href="#" @click.prevent="$dispatch('dom::remove_parent', $event)"  data-parent="span.list-group-item-danger" data-confirm="Really delete this tab?" class="label label-danger"><i class="fa fa-trash-o"></i> Remove</a>-->
                                            <!--</div>-->
                                        <!--</span>-->
                                        <!--<template v-if="tab.fields.length">-->
                                            <!--<span  v-for="f  in  tab.fields" style="cursor: move"-->
                                                   <!--data-rel="{{ f }}" class="list-group-item list-group-item-info">-->
                                                <!--<b>{{ f }}</b> {{ model.fields[f].title }} ({{ model.fields[f].type }})-->
                                            <!--</span>-->
                                        <!--</template>-->
                                    <!--</template>-->
                                <!--</template>-->
                            </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <h5>Available fields (drag'n'drop to form fields)</h5>
                            <div style="max-height:450px;overflow:auto">
                            <div style="min-height:30px;"  data-rel="fields_stack"   class="list-group" v-sortable="sortableStackOptions">
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

      name: 'FormEdit',

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
                tabs: [],
                fields: [],
                availableFields:[],
                usedFields: [],
                form: {fields:[], tabs:{}},
                formKey: "",
                edit:false,
                newTabTitle: "",
                fieldsContainer:null,
                fieldsStackContainer:null,
                sortableStackOptions: {
                    group: { name: "cols", pull: true, put: false},
                    sort: false

                },

                sortableDestOptions: {
                    group: { name: "cols", pull: false, put: true},
                    sort: true,
                    onAdd: (evt) => {
                        var itemEl = evt.item;
                        var newField = {key:$(itemEl).data('rel')};
                        this.fields.splice(evt.newIndex, 0, newField);
                        this.availableFields.$remove($(itemEl).data('rel'));

                    },

                    onUpdate: (evt) => {
                        this.fields.move(evt.oldIndex,evt.newIndex);
                    },

                }


            }
        },

        events: {
            // control modal from outside via events

            'form::new'(type) {
                this.initEmptyForm();
                this.$broadcast('show::modal','form_modal');
                this.initFields();


            },

            'form::edit'(key) {
                this.edit = true;
                this.formKey = key;
                this.$broadcast('show::modal','form_modal');
                this.initFields(this.model.forms[key]);

            },


        },

        methods: {


            addTab() {

                if (this.newTabTitle == '') {
                    swal('Oh no : (', 'Please, enter the tab title', 'warning');
                    return;
                }

                var tab = {tab:true, alias:'tab_'+this.tabs.length, title:this.newTabTitle};
                if (this.tabs.length == 0)
                {
                    this.fields.splice(0,0, tab);
                } else {
                    this.fields.push(tab);
                }

                this.tabs.push(tab);
                this.newTabTitle = '';

            },


            deleteField(key, rel) {
                this.fields.splice(key,1);
                if (rel) {
                    this.availableFields.push(rel);
                    this.availableFields.sort();
                }
            },

            initFields(form) {

                this.$set('tabs', []);

                if (!this.edit)
                {
                    this.$set('fields', []);
                    this.$set('availableFields', Object.keys(this.model.fields).sort());

                } else {

                    var fields = [];
                    var availFields = Object.keys(this.model.fields);
                    availFields.sort();

                    if (this.$parent.getFormType(form) == 'simple') {
                        form.forEach(function (f) {
                            fields.push({key: f});
                        });
                    } else if (this.$parent.getFormType(form) == 'tabbed') {

                        for (var tabid in form) {

                            let tab = JSON.parse(JSON.stringify(form[tabid]));
                            tab.alias = tabid;
                            tab.tab = true;
                            fields.push(tab);
                            this.tabs.push(tab);
                            if (tab.fields) {

                                tab.fields.forEach(function (f) {
                                    fields.push({key: f});
                                    availFields.$remove(f);
                                });
                                delete tab.fields;
                            }
                        }
                    }

                    this.$set('fields', fields);
                    this.$set('availableFields', availFields);
                }
            },

            initEmptyForm() {

                this.form = {fields: [], tabs: {}}

                if (typeof  this.model.forms == 'undefined' || !Object.keys(this.model.forms).length) {
                    this.formKey = "default";
                } else {
                    this.formKey = "form_" + (Object.keys(this.model.forms).length + 1);
                }

                this.edit = false;


            },


            hide() {

                this.$broadcast('hide::modal', 'form_modal')
            },

            save() {

                Actions.validateForm($('form#form_form'), () => {


                    if (this.tabs.length <=0) {
                        var form_arr = [];
                        this.fields.forEach(function (f, i) {
                           form_arr.push(f.key);
                        });

                        if (!Object.keys(this.model.forms).length) {
                            let forms = {};
                            forms[this.formKey] = form_arr;
                            this.$set('model.forms',forms);
                        } else {
                            Vue.set(this.model.forms, this.formKey, form_arr);
                        }

                        //Vue.set(this.model.forms,this.formKey,form_arr);
                    } else {


                        var first_tab_ind  =  0;
                        this.fields.every((f, i) => {
                           if (f.tab) {
                               first_tab_ind = i;
                               return false;
                           }
                           return true;
                        });

                        if (first_tab_ind>0)
                        {
                            this.fields.move(first_tab_ind, 0);
                        }

                        var form_obj = {};
                        var current_alias = '';
                        this.fields.forEach((f, i) => {
                            if (f.tab) {
                                delete f.tab;
                                current_alias = f.alias;
                                delete f.alias;
                                form_obj[current_alias] = f;
                                form_obj[current_alias].fields = [];
                            } else  {
                                form_obj[current_alias].fields.push(f.key);
                            }
                        });

                        if (!Object.keys(this.model.forms).length) {
                            let forms = {};
                            forms[this.formKey] = form_obj;
                            this.$set('model.forms',forms);
                        } else {
                            Vue.set(this.model.forms, this.formKey, form_obj);
                        }
                        //console.log(form_obj);


                    }


                    this.hide();

                });

            },

//            initDnd () {
//
//                this.initAvailFields();
//                this.fieldsContainer = $('#form_form div[data-rel=fields_used]').first();
//                this.fieldsStackContainer = $('#form_form div[data-rel=fields_stack]').first();
//
//                var fc = this.fieldsContainer;
//                var fs = this.availableFields;
//                var form = this.form;
//                var vm = this;
//                this.fieldsStackContainer.sortable({
//                    connectWith: this.fieldsContainer,
//                    tolerance: 'pointer',
//                    update: function (event, ui) {
//
//                        vm.recollectUsedFields();
//
//                        var trgt = ui.item.parents('div').first();
//                        if (trgt.data('rel') == 'fields_used')
//                        {
//                            //in
//
//                            ui.item.remove();
//
//                        } else {
//                            //out
//                            if (fs.indexOf(ui.item.data('rel'))<0) {
//                                fs.push(ui.item.data('rel'));
//                            }
//                        }
//
//
//                    }
//                }).disableSelection();
//
//                this.fieldsContainer.sortable({
//                    connectWith: this.fieldsStackContainer,
//                    tolerance: 'pointer',
//                    update: function (event, ui) {
//
//
//                    }
//
//                }).disableSelection();
//
//
//            },

//            recollectUsedFields() {
//
//                var  tabs_el = this.fieldsContainer.find('*[data-form_tab]');
//
//                if (tabs_el.length) {
//                    var tab_index = 0;
//                    var fields = {0: []};
//                    //console.log(tabs_el.get(0));
//                    var first_tab = $(tabs_el.get(0));
//                    var tabs = [getTabData(first_tab, 0)];
//                    first_tab.hide();
//
//                    this.fieldsContainer.children(':visible').each(function (i) {
//
//
//                        if ($(this).data('form_tab')) {
//
//
//                            tab_index++;
//                            tabs.push(getTabData($(this), tab_index));
//                            fields[tab_index] = [];
//
//
//                        } else {
//                            fields[tab_index].push($(this).data('rel'));
//                        }
//                    });
//
//                    first_tab.show();
//
//                    var set_tabs = {};
//
//                    for (let i=0; i<tabs.length; i++)
//                    {
//                        set_tabs[tabs[i].key] = Object.assign({},tabs[i]);
//                        set_tabs[tabs[i].key].fields = fields[i];
//                        delete set_tabs[tabs[i].key].key;
//                    }
//
//                    this.$set('form',{fields:[], tabs:set_tabs} );
//
//
//                } else {
//                    var newForm = [];
//                    this.fieldsContainer.find('*[data-rel]').each (function (i) {
//                        newForm.push($(this).data('rel'));
//                    });
//                    this.$set('form',{fields:newForm, tabs:{}} );
//                }
//
//                function getTabData(elem, i) {
//                    var tab = {};
//                    tab.title = $.trim(elem.find('span').text());
//                    if (elem.data('view')) {
//                        tab.view = elem.data('view');
//                    }
//                    var key = 'tab_' + i;
//                    if (elem.data('key')) {
//                        key = elem.data('key');
//                    }
//                    tab.key = key;
//
//                    return tab;
//                }
//
//            }
        },


        watch: {

        }

    }
</script>
