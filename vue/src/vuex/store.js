import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const default_model = {
    acl:'',
    title_field:'',
    track_history: '',
    forms: {},
    fields: {},
    scopes: {},
};

const state = {
    model: default_model,
    config: {
        'field_section_config':{}
    },
}

const mutations = {
    SET_MODEL (state, model) {

        //console.log(state.model);
        state.model = Object.assign({},default_model, model);
        //console.log(state.model);

    },

    SET_CONFIG (state, config) {
        state.config = config;
    },

}

export default new Vuex.Store({
    state,
    mutations
})
