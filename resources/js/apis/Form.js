import Api from './Api';

export default {
    async getAllForms() {
        return Api.get('/forms');
    },
    async getSpecificForm(slug) {
        return Api.get('/form/' + slug)
    },
    async getSpecificPrivateForm(token) {
        return Api.get('/private_form/' + token)
    },
    async getSpecificFormWithAuth(slug) {
        return Api.get('/form/authenticated/' + slug)
    },
    async postFormCompletion(formCompletion, slug) {
        return Api.post('/form/complete/' + slug, formCompletion);
    },
    async postPrivateFormCompletion(formCompletion, token) {
        return Api.post('/form/private_complete/' + token, formCompletion);
    },
    async postCreateForm(newForm) {
        return Api.post('/forms/create', newForm);
    },
    async deleteForm(slug) {
        return Api.delete('/forms/' + slug);
    },
    async postDuplicateForm(props) {
        return Api.post('/form/duplicate', props)
    },
    async getFormResults(slug) {
        return Api.get('/form/results/' + slug)
    },
    async getPublicFormResults(slug) {
        return Api.get('/form/public_results/' + slug)
    },
    async getFormResultsDownload(slug) {
        return Api.get('/form/results/' + slug + "/download", {responseType: "blob"})
    },
    async postPublishFormResults(data, slug) {
        return Api.post('/form/results/' + slug + "/publish_results", data)
    }
};
