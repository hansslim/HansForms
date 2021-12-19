import Vue from "vue";
import Vuex from "vuex";
import VuexPersistence from 'vuex-persist';
import MyStore from './MyStoreClass'

//store for auth
Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        authenticated: false,
        user: null
    },
    getters: {
        authenticated(state) {
            return state.authenticated;
        },
        user(state) {
            return state.user;
        }
    },
    mutations: {
        SET_AUTHENTICATED(state, value) {
            state.authenticated = value;
        },
        SET_USER(state, value) {
            state.user = value;
        }
    },
    actions: {
        login({ commit }, user) {
            commit('SET_AUTHENTICATED', true);
            commit('SET_USER', user);
        },
        logout({ commit }) {
            commit('SET_AUTHENTICATED', false);
            commit('SET_USER', null);
        },
        updateUser({ commit }, user) {
            commit('SET_USER', user);
        }
    },
    plugins: [new VuexPersistence().plugin]
});
export default store;

//store for create form page
export const createFormStore = new MyStore();
export const createFormChoicesStore = new MyStore();
