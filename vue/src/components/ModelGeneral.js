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
        // ent_name: {
        //   type: 'text',
        //   label: 'Entity name *'
        // },
        acl: {
          type: 'select',
          label: 'Acl',
          options: this.commonConfig.acls,
          col: 1
        },
        tree: {
          type: 'switch',
          label: 'Model is tree',
          col: 1
        },
        title_field: {
          label: 'Title field',
          type: 'select',
          options: this.modelConfig.table_columns,
          col: 2
        },
        track_history: {
          label: 'Audit trail. Track model changes',
          type: 'select',
          options: this.commonConfig.track_history_options,
          col: 2
        }
      }
    }
  },
  mixins: [
    ModelMixin
  ],
  components: {
    Dform,
    ModelEdit
  }
}

