import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const default_model = {
    acl:'',
        title_field:'',
        track_history: '',
};

const state = {
    model: default_model,
    config: {},
}

const mutations = {
    SET_MODEL (state, model) {

        state.model = Object.assign({},default_model, model);
        console.log(state.model);

    },

    SET_CONFIG (state, config) {
        state.config = config;
    },

}

export default new Vuex.Store({
    state,
    mutations
})
