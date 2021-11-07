import Api from './Api';

export default {
    async getAllForms() {
        return Api.get('/forms');
    },
    async getSpecificForm(slug) {
        return Api.get('/form/' + slug)
    },
    async postFormCompletion(formCompletion, slug) {
        return Api.post('/form/complete/' + slug, formCompletion);
    }
};
