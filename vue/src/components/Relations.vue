<template>
    <label>

        <select class="form-control default_select" id="rtype" v-model.sync="current_relation.relation"
                style="display:inline; width:250px;">
            <option value="">Choose relation type</option>
            {%  for rel in wizard.getRelations() %}
            <option value="{{ rel }}">{{ rel }}</option>
            {% endfor %}
        </select>
        <a href="#" class="btn btn-success" @click="addRelation"><i class="fa fa-plus"></i>
            Add relation</a>
    </label>

    <table class="table table-striped">
        <tr>

            <th width="50px">Type</th>
            <th width="150px">Attribute name</th>
            <th width="150px">Edit Type</th>
            <th>Title</th>
            <th width="150px;">Relation model</th>
            <th width="100px"></th>
        </tr>
        <tr v-for="(key, rel) in $parent.model.fields | filterBy isRelation">
            <td><span class="text-success">
                                            <img :title="rel.relation"
                                                 :src="'/vendor/crud-wizard/images/icons/'+ rel.relation+ '.png'"/></span></td>
            <td>{{ key }}</td>
            <td>{{ rel.type }}</td>
            <td>{{ rel.title }}</td>
            <td>{{ rel.model }}</td>
            <td><a class="text-info" style="font-size: 20px;" href="#" @click="editRelation(key)"><i
                    class="fa fa-edit"> </i></a>
                &nbsp;&nbsp;&nbsp;
                <a class="text-danger" href="#" @click.prevent="this.$dispatch('delete_field',key)"
                   style="font-size: 20px;"><i class="fa fa-trash-o"> </i></a>
            </td>
        </tr>

    </table>
    <label>

        <select class="form-control default_select" id="rtype" v-model.sync="current_relation.relation"
                style="display:inline; width:250px;">
            <option value="">Choose relation type</option>
            {%  for rel in wizard.getRelations() %}
            <option value="{{ rel }}">{{ rel }}</option>
            {% endfor %}
        </select>
        <a href="#" class="btn btn-success" @click.prevent="addRelation"><i class="fa fa-plus"></i>
            Add relation</a>
    </label>

    <relation-modal></relation-modal>

</template>

<script>
//    import HeaderComponent from './components/header.vue'
    import RelationModal from './RelationModal.vue'

    export default{
        components:{
            RelationModal
        },

        data(){
            return {

                current_relation:{}
            }
        },
        methods: {

            addRelation() {
                this.$broadcast('show::modal', 'relation_modal');
            },

            isRelation (row) {
                if (row.$value.relation)
                {
                    return row;
                }
            },
        }

    }
</script>
