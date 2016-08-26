import Vue from 'vue'
import swal from 'sweetalert2'

const apiUrl = 'http://admin.mk5.app/crud_setup/wizard/'

var Actions = {}

export default Actions

Actions.apiUrl = apiUrl

Actions.fetchConfig = function ({dispatch}, table, loadCommon = true) {
  dispatch('SET_TABLE', table)
  var pageUrl = apiUrl + 'getWizardConfig'
  Vue.http.get(pageUrl, {params: {args: [table, +loadCommon]}})
    .then((response) => dispatch('SET_CONFIG', response.json()))
    .catch((error) => Promise.reject(error))
}

Actions.fetchModels = function ({dispatch}) {
  var pageUrl = apiUrl + 'getWizardModelsConfig'
  Vue.http.get(pageUrl)
    .then((response) => dispatch('SET_MODELS', response.json()))
    .catch((error) => Promise.reject(error))
}

// Actions.fetchModel = function ({dispatch}, table) {
//   var pageUrl = apiUrl + 'getModelConfig'
//   return Vue.http.get(pageUrl, {params: {args: [table]}})
//     .then((response) => dispatch('SET_MODEL', response.json()))
//     .catch((error) => Promise.reject(error))
// }

// Actions.validateForm = function ($form, callback) {
//   let bootstrapValidator = $form.data('bootstrapValidator')
//   if (bootstrapValidator) {
//     bootstrapValidator.validate()
//     console.log(bootstrapValidator)
//     console.log(bootstrapValidator.isValid())
//     if (!bootstrapValidator.isValid()) {
//       swal('Please, fill all the required fields', '', 'warning')
//       return false
//     }
//   }
//
//   return callback()
// }



