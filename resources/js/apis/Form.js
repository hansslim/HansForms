import Api from './Api';

export default {
    async getAllForms() {
        return Api.get('/forms');
    },
    async getSpecificForm(slug) {
        return Api.get('/form/' + slug)
    },
    async getSpecificFormWithAuth(slug) {
        return Api.get('/form/authenticated/' + slug)
    },
    async postFormCompletion(formCompletion, slug) {
        return Api.post('/form/complete/' + slug, formCompletion);
    },
    async postCreateForm(newForm) {
        return Api.post('/forms/', newForm);
    },
    async deleteForm(slug) {
        return Api.delete('/forms/' + slug);
    },
    async postDuplicateForm(props) {
        return Api.post('/form/duplicate/', props)
    },
    async getFormResults(slug) {
        return Api.get('/form/results/' + slug)
    }
};
