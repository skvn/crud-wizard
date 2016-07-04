Vue.config.delimiters = ['<%', '%>']


Vue.component('Modal', {
    template: '#modal-template',
    props: ['show', 'onClose'],
    methods: {
        close: function () {
            this.onClose();
        }
    },
    ready: function () {
        document.addEventListener("keydown", (e) => {
            if (this.show && e.keyCode == 27) {
            this.onClose();
        }
    });
    }
});


Vue.component('relation-modal', {
    template: '#relation-modal-template',
    props: ['show', 'relation'],
    methods: {
        close: function () {
            this.show = false;
            this.initEmptyRelation();
        },
        save: function () {
            // Insert AJAX call here...
            this.close();
        }
    },

});

new Vue({

    el: '#wizard',
    data:
    {

        empty_relation: {
            'relation':'',
            'model': '',
            'on_delete':'',
            'ref_column':'',
            'editable':false,
            'form_field':'',
            'find': '',
            'model_obj':{'columns':[]},
            'local_key':'',
            'pivot':0,
            'pivot_table':'',
            'pivot_self_key':'',
            'pivot_foreign_key':'',
            'pivot_columns': []
        },
        current_relation: {},
        show_new_relation: false,
        model_loaded:false,
        model: {},
        table: "",
        base_url: "/admin/crud_setup/wizard/"
    },

    created: function () {

        this.initEmptyRelation();
    },
    ready: function() {
        this.fetchModel();
    },

    methods: {

        initEmptyRelation: function () {

            this.current_relation =  JSON.parse(JSON.stringify(this.empty_relation));

        },
        isRelation: function (row) {
            if (row.$value.relation)
            {
                return row;
            }
        },

        addRelation: function (){
            if (this.current_relation.relation == '')
            {
              alert('Choose relation type');
              return false;
            }

            this.show_new_relation = true;
        },
        deleteField: function(key) {
            if (confirm('Delete '+key+'?'))
            {
                Vue.delete(this.model.fields,key);
            }

        },

        fetchModel: function () {
            var vm = this;
            var page_url = vm.base_url +'getModelConfig'
            this.$http.get(page_url, {params:{args:[vm.table]}})
                .then(function (response) {
                    console.log(response.data);
                    vm.$set('model', response.data)
                    vm.$set('model_loaded', true)
                });
        },

        fillContainerWithRemoteData: function(container, page, args)
        {
            var vm = this;
            this.$http.get(page, args)
                .then(function (response) {
                    vm.$set(container, response.data)
                });
        },

        fillRelationModel: function (model, container) {

            var vm = this;
            vm.$set(container, []);
            var page_url = this.base_url +'getRelationModelData'
            this.fillContainerWithRemoteData(container, page_url, {params:{args:[model]}});

        },
        fillTableColumns: function (table, container) {

            var vm = this;
            vm.$set(container, []);
            var page_url = this.base_url +'getTableColumns'
            this.fillContainerWithRemoteData(container, page_url, {params:{args:[table]}});

        }


    },
    watch: {
        'current_relation.model': function (value) {
            if (value) {
                this.fillRelationModel(value, 'current_relation.model_obj');
            }
        },
        'current_relation.pivot_table': function (value) {
            if (value) {
                this.fillTableColumns(value, 'current_relation.pivot_columns');
            }
        }
    }

});