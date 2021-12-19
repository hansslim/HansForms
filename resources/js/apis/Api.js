import axios from 'axios';
import router from '../router';
import store from '../store';

const Api = axios.create({
    baseURL: 'http://localhost:8000/api'
});

Api.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        switch (error.response.status) {
            case 400:
                console.log("API bad request");
                break;
            case 404:
                console.log("API not found");
                break;
            case 410: {
                console.log("API gone");
                break;
            }
            case 401:
            case 419:
                console.log("API session expired/unauthorized");
                try {
                    store.dispatch('logout');
                    router.push('/login');
                } catch (e) {
                    console.log(e)
                }
                break;
            case 423: {
                console.log("API locked");
                break;
            }
            case 500:
                break;
            case 503:
                break;
            default:
                return Promise.reject(error);
        }
        return error.response
    });

export default Api;
