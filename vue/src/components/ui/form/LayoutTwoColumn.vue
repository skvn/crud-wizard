<template>

    <component :is="field.type" :model="model" :config="field" v-for="field in structure"></component>

</template>
<script>
    import TextField from './Text.vue'
    import SelectField from './Select.vue'

    export default {
        props: {
            model: {
                type: [Object],
                required: true,
                twoWay: true
            },
            structure: {
                type: [Object],
                required: true
            }
        },
        components: {
            TextField,
            SelectField
        },
        data () {
            return {
            }
        },
    }
</script>
