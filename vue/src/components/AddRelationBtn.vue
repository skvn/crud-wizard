<template>
    <label>

        <select class="form-control default_select" id="rtype" v-model.sync="$parent.new_relation_type"
                style="display:inline; width:250px;">
            <option value="">Choose relation type</option>

            <option v-for="rel in $parent.$parent.config.relation_options" v-bind:value="rel">
                {{ rel }}
            </option>


        </select>
        <a href="#" class="btn btn-success" @click.prevent="$parent.addRelation"><i class="fa fa-plus"></i>
            Add relation</a>
    </label>
</template>

<script>

    export default{

    }
</script>
