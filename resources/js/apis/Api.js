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
                return {data: {error: '400'}};
            case 404:
                console.log("API not found");
                return {data: {error: '404'}};
            case 410: {
                console.log("API gone");
                return {data: {error: '410'}};
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
                return {data: {error: '401'}};
            case 423: {
                console.log("API locked");
                return {data: {error: '423'}};
            }
            case 503:
            case 500:
                alert('Internal Server Error');
                break;
            default:
                return Promise.reject(error);
        }
    });

export default Api;
