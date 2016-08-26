import template from './html/index.html'
import store from '../vuex/store'
import Actions from '../vuex/actions'

import { getModels, modelsLoaded } from '../vuex/getters'

export default{
  data () {
    return {

    }
  },

  template: template,
  store,
  vuex: {
    getters: {
      models: getModels,
      modelsLoaded: modelsLoaded
    },
    actions: {
      fetchModels: Actions.fetchModels
    }

  },

  route: {
    data ({to}) {
      if (!this.modelsLoaded) {
        this.fetchModels()
      }
    }
  }
}

