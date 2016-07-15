import Vue from 'vue'
import Resource from 'vue-resource'
import Router from 'vue-router'
import Vuex from 'vuex'

import App from './components/App.vue'
import General from './components/General.vue'

import store from './vuex/store'


Vue.use(Resource)
Vue.use(Router)
Vue.use(Vuex)


// Vue.config.delimiters = ['<%', '%>']

export var router = new Router()

router.map({
    '/general': {
        component: General
    }

})

router.redirect({
    '*': '/general'
})



router.start({
    store,
    components: { App }
}, '#app')

//router.start(App, '#app')
