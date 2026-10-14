require('./bootstrap');

import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import moment from 'vue-moment';
import axios from 'axios';

Vue.use(Vuetify);
Vue.use(moment);

axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': window.csrf_token
};

const VueUploadComponent = require('vue-upload-component');

Vue.component('file-upload', VueUploadComponent);
Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('Chat', require('./components/Chat.vue'));
Vue.component('PrivateChat', require('./components/PrivateChat.vue'));
Vue.component('simulation-list', require('./components/simulation/SimulationList.vue'));
Vue.component('media-searchlist', require('./components/media-searchlist.vue'));

const app = new Vue({
    el: '#app',
    data() {
        return {
            loading:true,
        }
    }
});
