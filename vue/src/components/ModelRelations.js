import RelationEdit from './ModelRelationEdit'
import AddRelationBtn from './AddRelationBtn.vue'
import template from './html/model_relations.html'
import swal from 'sweetalert2'
import ModelMixin from '../mixins/ModelMixin'
import ModelEdit from './ModelEdit'

export default{
  components: {
    RelationEdit,
    AddRelationBtn,
    ModelEdit
  },
  mixins: [
    ModelMixin
  ],
  template,
  events: {
    'relation::add' (type) {
      if (type === '') {
        swal('Choose relation type', '', 'warning')
        return false
      }
      this.$broadcast('relation::new', type)
    }
  },
  methods: {

    editRelation (key) {
      this.$broadcast('relation::edit', key)
      this.$broadcast('show::modal', 'relation_modal')
    },

    isRelation (row) {
      if (row.$value.relation) {
        return row
      }
    }
  }

}

