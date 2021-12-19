import axios from 'axios';
import Vue from 'vue';
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
                return error.response
            case 404:
                console.log("API not found");
                return error.response
            case 410: {
                console.log("API gone");
                return error.response
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
                return error.response
            case 423: {
                console.log("API locked");
                return error.response
            }
            case 500: return error.response
            case 503: return error.response
            default:
                return Promise.reject(error);
        }
    });

export default Api;
