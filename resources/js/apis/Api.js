import axios from 'axios';
import router from '../router';
import UserApi from './User';

const Api = axios.create({
    baseURL: 'http://localhost:8000/api'
});

Api.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        switch (error.response.status) {
            case 401:
            case 419:
            case 503:
                UserApi.logout().then(() => router.push('/login'));
                break;
            case 500:
                alert('Internal Server Error');
                break;
            default:
                return Promise.reject(error);
        }
    });

export default Api;
