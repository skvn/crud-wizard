<template>
    <label>

        <select class="form-control default_select" id="rtype" v-model.sync="$parent.new_field_type"
                style="display:inline; width:250px;">
            <option value="">Choose field type</option>

            <option v-for="(val, text) in $parent.$parent.config.field_options" v-bind:value="val">
                {{ text }}
            </option>


        </select>
        <a href="#" class="btn btn-success" @click.prevent="$parent.addField"><i class="fa fa-plus"></i>
            Add field</a>
    </label>
</template>

<script>

    export default{

    }
</script>
