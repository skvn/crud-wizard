import Vue from 'vue'
import Resource from 'vue-resource'
import Router from 'vue-router'
import Vuex from 'vuex'

import App from './components/App.vue'
import General from './components/General.vue'
import Relations from './components/Relations.vue'
import Fields from './components/Fields.vue'

import store from './vuex/store'


Vue.use(Resource)
Vue.use(Router)
Vue.use(Vuex)


// Vue.config.delimiters = ['<%', '%>']

export var router = new Router()

router.map({
    '/general': {
        component: General

    },

    '/relations': {
        component: Relations
    },

    '/fields': {
        component: Fields
    },

})

router.redirect({
    '*': '/general'
})

router.beforeEach(function (transition) {

    var form = $('form.validate').first();
    if (form.length) {
        var bootstrapValidator = form.data('bootstrapValidator');
        if (bootstrapValidator ) {
            bootstrapValidator.validate();
            if (!bootstrapValidator.isValid()) {
                return false;
            }
        }

    }
    transition.next()
});

router.afterEach(function (transition) {

    $('.nav-pills').find('li').removeClass('active');
    $('.nav-pills').find('a[href="#!'+transition.to.path+'"]').parent().addClass('active');

});

router.start({
    store,
    components: { App }
}, '#app')

//router.start(App, '#app')
