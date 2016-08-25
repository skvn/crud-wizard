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
  config: {
    'acls': {},
    'field_section_config': {}
  },
  configLoaded: false,
  modelsLoaded: false

}

const mutations = {
  SET_MODEL (state, model) {
    // console.log(state.model);
    state.model = Object.assign({}, defaultModel, model)
    // state.configLoaded = true;
    // console.log(state.model);
  },

  SET_TABLE (state, table) {
    state.table = table
  },

  RESET_TABLE (state, table) {
    state.table = ''
    state.configLoaded = false
    state.model = {}
  },

  SET_MODELS (state, models) {
    state.models = JSON.parse(JSON.stringify(models))
    state.modelsLoaded = true
  },

  SET_CONFIG (state, config) {
    state.config = config
    state.configLoaded = true
  }

}

export default new Vuex.Store({
  state,
  mutations
})
