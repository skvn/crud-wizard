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
        document.addEventListener("keydown", function (e){
            if (this.show && e.keyCode == 27) {
            this.onClose();
        }
    });
    }
});


Vue.component('relation-modal', {
    template: '#relation-modal-template',
    props: ['show', 'edit', 'relation'],
    created: function () {

      this.$on('show_relation_modal', function () {
          this.show = true;
      })
    },
    methods: {
        close: function () {
            this.show = false;
            this.$dispatch('empty_relation');
        },
        save: function () {
            this.$dispatch('save_relation');
            this.close();
        }
    }


});

new Vue({

    el: '#wizard',
    data:
    {

        empty_relation: {
            'relation':'',
            'model': '',
            'on_delete':'',
            'field':'',
            'editable':false,
            'type':'',
            'find': '',
            'model_obj':{'columns':[],'find_methods':[]},
            'pivot':0,
            'pivot_table':'',
            'pivot_self_key':'',
            'pivot_foreign_key':'',
            'pivot_columns': []
        },
        current_relation: {},
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

    events: {
        'empty_relation': function () {
            this.initEmptyRelation();
        },
        'save_relation': function () {
            this.saveRelation();
        },

    },

    methods: {


        addRelation: function (){
            if (this.current_relation.relation == '')
            {
              alert('Choose relation type');
              return false;
            }

            this.$broadcast('show_relation_modal');

        },
        cloneObject: function (obj) {
            return JSON.parse(JSON.stringify(obj));
        },

        deleteField: function(key) {
            if (confirm('Delete '+key+'?'))
            {
                Vue.delete(this.model.fields,key);
            }

        },
        editRelation: function (key) {
            this.current_relation = this.model.fields[key];
            this.current_relation.key = key;
            this.$broadcast('show_relation_modal');

        },
        fetchModel: function () {
            var vm = this;
            var page_url = vm.base_url +'getModelConfig';
            this.$http.get(page_url, {params:{args:[vm.table]}})
                .then(function (response) {
                    console.log(response.data);
                    vm.$set('model', response.data);
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

        },

        initEmptyRelation: function () {

            this.current_relation =  this.cloneObject(this.empty_relation);

        },
        isRelation: function (row) {
            if (row.$value.relation)
            {
                return row;
            }
        },
        saveRelation: function () {
            delete this.current_relation.model_obj;
            Vue.set(this.model.fields,this.current_relation.key, this.cloneObject(this.current_relation));

        },

        saveModel: function () {
            var model_json = JSON.stringify(this.model);
            //this.model_loaded = false;
            $("html, body").animate({ scrollTop: 0 }, "slow");
            this.$http.post('/admin/crud_setup/'+this.table, {model_json:model_json})
                .then(function (response) {
                    console.log(response.data);
                });
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