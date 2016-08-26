import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const defaultModel = {
  acl: '',
  tree: false,
  title_field: '',
  track_history: '',
  forms: {},
  fields: {},
  scopes: {}
}

const state = {
  table: '',
  model: defaultModel,
  models: {},
  commonConfig: {
    'acls': {},
    'field_section_config': {}
  },
  modelConfig: {},
  configLoaded: false,
  modelsLoaded: false

}

const mutations = {
  SET_CONFIG (state, modelReceived) {
    state.model = Object.assign({}, defaultModel, modelReceived['model'])
    state.modelConfig = modelReceived['model_config']
    if ('common_config' in modelReceived) {
      state.commonConfig = modelReceived['common_config']
    }
    state.configLoaded = true
  },
  SET_TABLE (state, table) {
    state.table = table
  },
  RESET_TABLE (state, table) {
    state.table = ''
    state.configLoaded = false
    state.model = {}
    state.modelConfig = {}
  },
  SET_MODELS (state, models) {
    state.models = models
    state.modelsLoaded = true
  }
}

export default new Vuex.Store({
  state,
  mutations
})
