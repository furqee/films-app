import Vue from "vue";
import App from "./components/App";
import router from "./router";
import store from "./store";
import axios from "axios";
import {extend} from 'vee-validate';
import {required, email, confirmed, min, image } from 'vee-validate/dist/rules';
import { ValidationProvider, ValidationObserver } from 'vee-validate';
import Datepicker from 'vuejs-datepicker';
import VueMoment from 'vue-moment';
import VueSweetalert2 from 'vue-sweetalert2';
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

/**
 * register components globally
 */
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
Vue.component('datepicker', Datepicker);

/**
 * extending validators
 */
extend('required', {
    ...required,
    message: 'This field is required.'
});

extend('email', {
    ...email,
    message: 'This field must be valid email.'
});

extend('confirmed', {
    ...confirmed,
    message: 'Password and Confirm password must be same.'
});

extend('min', {
    ...min,
    message: 'Minimum 5 characters required.'
});

extend('image', {
    ...image,
    message: 'Image is invalid.'
});

/**
 * Inject Dependencies
 */
Vue.use(VueSweetalert2);
Vue.use(VueMoment);

Vue.config.productionTip = false;

axios.defaults.withCredentials = true;

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 422) {
            store.commit("setErrors", error.response.data.errors);
        } else if (error.response.status === 401) {
            store.commit("auth/setUserData", null);
            localStorage.removeItem("authToken");
            router.push({ name: "Login" });
        } else {
            return Promise.reject(error);
        }
    }
);

/**
 * Initialize App
 */
new Vue({
    router,
    store,
    render: h => h(App)
}).$mount("#app");
