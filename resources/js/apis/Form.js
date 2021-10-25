import Api from './Api';

export default {
    async getAllForms() {
        return Api.get('/forms');
    },
    async getSpecificForm(slug) {
        return Api.get('/form/' + slug)
    }
};
