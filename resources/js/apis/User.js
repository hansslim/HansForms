import Api from './Api';

export default {
    async login(user) {
        await Api.get('/csrf-cookie');
        return Api.post('/login', user);
    },
    logout() {
        return Api.post('/logout');
    },
    register(user) {
        return Api.post('/register', user);
    },
    async loggedUser() {
        return Api.get('/logged_user');
    },
    async changePassword(data) {
        return Api.put('/change_password', data);
    },
    async deleteAccount(password) {
        return Api.post('/delete_account', password);
    },
};
