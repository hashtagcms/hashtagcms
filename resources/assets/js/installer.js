/** Axios setup **/
import axios from "axios";
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    axios.defaults.headers.common['Authorization'] = 'Bearer '+token.content;
    axios.defaults.withCredentials = true;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
window.axios = axios;

import {createApp} from "vue";
import SiteInstaller from './installer/installer.vue';

const app = createApp({
    components: {
        'site-installer':SiteInstaller

    }
}).mount('#app');
//window.Vue = app;
