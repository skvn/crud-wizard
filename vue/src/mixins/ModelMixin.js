import Vue from 'vue'
import store from '../vuex/store'
import Actions from '../vuex/actions'
import {getModel, getConfig, getTable, configLoaded} from '../vuex/getters'
import swal from 'sweetalert2'

export default {
  store,
  vuex: {
    getters: {
      model: getModel,
      table: getTable,
      config: getConfig,
      configLoaded: configLoaded
    },
    actions: {
      fetchModel: Actions.fetchModel,
      fetchConfig: Actions.fetchConfig
    }
  },
  events: {

    delete_field (key) {
      this.deleteField(key)
    },
  },
  methods: {

    saveModel () {
      // var form = $('form.validate').first()
      if (form.length) {
        var bootstrapValidator = form.data('bootstrapValidator')
        if (bootstrapValidator) {
          bootstrapValidator.validate()
          if (!bootstrapValidator.isValid()) {
            return false
          }
        }
      }

      swal(
        {
          title: "Are you sure?",
          text: "Please check everything!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, save it!",
          showLoaderOnConfirm: true,

        })
        .then(() => {
          var pageUrl = Actions.apiUrl + 'saveModel'
          this.$http.post(pageUrl, {args: [this.table, JSON.stringify(this.model)]}).then ((resp) => {
            var res = resp.json()
            if (res && typeof res.success !== 'undefined' && res.success) {
              if (res.migrations) {
                swal(
                  {
                    title: "Migrations created",
                    text: "Some migrations were created. You can run them right now or review and run `artisan migrate` later",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Run them!",
                    cancelButtonText: "Forget it"
                  })
                  .then(() => {
                    var pageUrl = Actions.apiUrl + 'runMigrations'
                    this.$http.post(pageUrl, {}).then((resp) => {
                      var res = resp.json()
                      if (res && typeof res.success !== 'undefined' && res.success) {
                        swal('Ohh yeah', '', 'success')
                      } else {
                        swal('Ohh no', '', 'error')
                      }
                    })
                  }, () => {
                  })
              } else {
                swal('Ohh yeah', '', 'success')
              }
            } else {
              swal('Ohh no', '', 'error')
            }
          })
        }, () => {
        })
    },

    deleteField (key) {
      swal(
        {
          title: "Are you sure?",
          text: "You will not be able to recover this field!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!"

        })
        .then(() => {
          Vue.delete(this.model.fields, key)
          swal(
            'Deleted!',
            'The field has been deleted.',
            'success'
          )
        }, () => {})
    }
  }
}

