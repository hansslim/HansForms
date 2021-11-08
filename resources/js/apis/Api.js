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
                try {
                        this.$store.logout();
                        router.push('/login');
                }
                catch (e) {console.log(e)}
                break;
            case 500:
                alert('Internal Server Error');
                break;
            default:
                return Promise.reject(error);
        }
    });

export default Api;
