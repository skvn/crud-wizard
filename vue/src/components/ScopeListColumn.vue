<template>
            <tr>
                <td style="width:20px;cursor: move" class="drag_cols" ><i class="fa fa-arrows-v"></i></td>
                <td style="width:350px;" nowrap="nowrap">

                    <div class="form-group">
                    <label>Data provider</label>
                    <br>
                    <select  class="form-control default_select" v-model="data_type" >
                            <option value="prop">Model property</option>
                            <option value="rel">Relation property</option>

                     </select><br>


                    <select  class="form-control default_select"   v-model="column.data" v-if="data_type=='prop'" >
                        <option value="">Choose property</option>
                        <option v-for="prop in data_fields" v-bind:value="prop">
                            {{ prop }}
                        </option>
                    </select>

                    <select  class="form-control default_select"  v-model="relation" v-if="data_type=='rel'" >
                        <option value="">Choose relation</option>
                        <option v-for="(k, rel) in relations" v-bind:value="k">
                            {{ rel.title }} ({{ k }})
                        </option>
                    </select>

                    <select  class="form-control default_select"  v-model="relation_prop" v-if="data_type=='rel'" >
                        <option value="">Choose relation property</option>
                        <option v-for="k in relation_attrs" v-bind:value="k">
                            {{ k }}
                        </option>
                    </select>
                     </div>


                </td>
                <td valign="top" style="width:150px;">
                    <div class="form-group">
                    <label>Title
                        <input type="text" data-rel="title" class="form-control" name="title"  :value="column.title" v-model="column.title"  placeholder="Title *" required style="width: 150px" >
                    </label>
                    </div>
                    <div class="form-group">
                        <label>
                            Formatter
                            <select class="form-control default_select" style="max-width: 150px;" v-model="column.format" >
                                <option value="">No formatter</option>
                                <option v-for="f in config.formatters" v-bind:value="f.name">
                                    {{ f.name }} ({{ f.description }})
                                </option>
                            </select>
                        </label>
                    </div>

                </td>


                <td valign="top"  style="width:100px;" >
                    <div class="checkbox">
                        <label>
                            <input type="checkbox"  name="invisible" :value="column.invisible"  v-model="column.invisible"  > Invisible
                        </label>
                    </div>


                    <template v-if="!column.invisible">
                        <label>Width<input type="text" class="form-control" :value="column.width" v-model="column.width" placeholder="Width" style="width:50px"></label>
                    </template>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox"  name="searchable" v-model="column.searchable" > Search
                        </label>
                    </div>

                    <template v-if="!column.invisible">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox"  name="orderable" v-model="column.orderable" > Sort
                        </label>
                    </div>



                    <div class="checkbox">
                        <label>
                            <input type="checkbox"  name="filterable" v-model="column.filterable"  > Filter
                        </label>
                    </div>
                    </template>
                </td>

                <td style="width:40px;" valign="middle"><a href="#"  @click.prevent="$dispatch('list_column::delete',key)"    class="label label-danger">
                    <i class="fa fa-trash-o"></i></a></td>


            </tr>
</template>

<script>

    import Vue from 'vue';
    import { getConfig, getModel, getRelations } from '../vuex/getters'
    import Actions from '../vuex/actions'

    export default{

        name: 'ScopeListColumn',

        data(){
            return{
                data_type: 'prop',
                relation: '',
                relation_prop: '',
                relation_attrs: [],
                data_fields: [],
            }
        },


        vuex: {
            getters: {
                config: getConfig,
                model: getModel,
                relations: getRelations
            }

        },

        props:  [
            'column'
        ],

        ready () {

            if (this.column.data.indexOf('::') >=0) {
                this.data_type = 'rel';
                this.relation = this.column.data.split('::')[0];
                this.relation_prop = this.column.data.split('::')[1];
            }

            this.data_fields = this.config.attrs.uniqueMerge(Object.keys(this.model.fields)).sort();

        },

        watch: {
            'relation': function (val, prev) {
                if (prev) {
                    this.relation_prop = '';

                }
                if (val) {
                    var model = this.model.fields[val].model;
                    if (typeof model != 'undefined') {
                        var page_url = Actions.apiUrl + 'getModelAttributes'
                        this.$http.get(page_url, {params: {args: [model]}}).then((resp)=> {
                            this.$set('relation_attrs', resp.json())
                        });
                    }

                    this.column.data = val+'::'+this.relation_prop;

                }
            },

            'relation_prop': function (val, prev) {
                this.column.data = this.relation +'::'+this.relation_prop;
            },

        }

    }
</script>
