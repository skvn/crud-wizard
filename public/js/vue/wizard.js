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
            this.relation = {};
        },
        save: function () {
            // Insert AJAX call here...
            this.close();
        }
    }
});

new Vue({

    el: '#wizard',
    data:
    {
        new_relation: {
            'relation':'',
            'model': '',
            'on_delete':'',
            'ref_column':'',
            'editable':false,
            'form_field':'',
            'find': '',
        },
        show_new_relation: false,

        model_loaded:false,
        model: {},
        table: "",
        base_url: "/admin/crud_setup/wizard/"
    },

    ready: function() {
        this.fetchModel();
    },

    methods: {
        isRelation: function (row) {
            if (row.$value.relation)
            {
                return row;
            }
        },

        addRelation: function (){
            if (this.new_relation.relation == '')
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
            page_url = vm.base_url +'getModelConfig'
            this.$http.get(page_url, {params:{args:[vm.table]}})
                .then(function (response) {
                    console.log(response.data);
                    vm.$set('model', response.data)
                    vm.$set('model_loaded', true)
                });
        }

    }

});