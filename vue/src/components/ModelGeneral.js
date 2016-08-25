import template from './html/model_general.html'
import ModelMixin from '../mixins/ModelMixin'
import Dform from './ui/form/Form.vue'
import ModelEdit from './ModelEdit'

export default {
  template,
  name: 'General',
  data () {
    return {
    }
  },
  computed: {
    structure: function () {
      return {
        ent_name: {
          type: 'text',
          label: 'Entity name *'
        },
        acl: {
          type: 'select',
          label: 'Acl',
          options: this.config.acls
        },
        tree: {
          type: 'switch',
          label: 'Model is tree'
        }
      }
    }
  },
  mixins: [
    ModelMixin
  ],
  components: {
    ModelEdit,
    Dform

  },

  route: {
    data ({to}) {
      this.fetchConfig(to.params.table)
      this.fetchModel(to.params.table)
    }
  }
}

