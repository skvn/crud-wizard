import Vue from 'vue'


const apiUrl = "/admin/crud_setup/wizard/";

var Actions = {};

export default Actions;

Actions.apiUrl = apiUrl;

Actions.fetchConfig = function ({ dispatch}, table) {
    var page_url = apiUrl +'getWizardConfig';
    Vue.http.get(page_url, {params:{args:[table]}})
        .then((response) => dispatch('SET_CONFIG', response.json()))
        .catch((error) => Promise.reject(error));
};

Actions.fetchModel = function ({ dispatch }, table) {
    var page_url = apiUrl +'getModelConfig';
    Vue.http.get(page_url, {params:{args:[table]}})
        .then((response) => dispatch('SET_MODEL', response.json()))
        .catch((error) => Promise.reject(error));

};

Actions.validateForm = function ($form, callback) {

    let bootstrapValidator = $form.data('bootstrapValidator');
    if (bootstrapValidator) {
        bootstrapValidator.validate();
        console.log(bootstrapValidator);
        console.log(bootstrapValidator.isValid());
        if (!bootstrapValidator.isValid()) {
            swal('Please, fill all the required fields', '', 'warning');
            return false;
        }
    }

    return callback();
};

Array.prototype.move = function (from, to) {
    this.splice(to, 0, this.splice(from, 1)[0]);
};

Array.prototype.uniqueMerge = function( a ) {
    for ( var nonDuplicates = [], i = 0, l = a.length; i<l; ++i ) {
        if ( this.indexOf( a[i] ) === -1 ) {
            nonDuplicates.push( a[i] );
        }
    }
    return this.concat( nonDuplicates )
};






