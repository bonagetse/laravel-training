/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import moment from 'moment';
import { Form, HasError, AlertError } from 'vform';
import Gate from "./Gate";


Vue.prototype.$gate = new Gate(window.user);
window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component('pagination', require('laravel-vue-pagination'));


//sweet alerts
import Swal from 'sweetalert2';
window.Swal = Swal;

//toastr 
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

window.Toast = Toast;

//configuring vue-progress bar
import VueProgressBar from 'vue-progressbar'
Vue.prototype.$gate = new Gate(window.user);

Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
})

//configure VueRouter
import VueRouter from 'vue-router'
Vue.use(VueRouter);

//creating roures with router
let routes = [
    { path: '/dashboard', component:require('./components/Dashboard.vue').default },
    { path: '/profile', component:require('./components/Profile.vue').default },
    { path: '/developer', component:require('./components/developer.vue').default },
    { path: '/users', component:require('./components/Users.vue').default },
    //the * refers to any route and the path must be the last route
    { path: '*', component:require('./components/notFound.vue').default }
];

const router = new VueRouter({
    routes ,// short for `routes: routes`,
    mode: 'history'
  })
  
  //REGISTER FILTERS GLOBLALY
  Vue.filter('textToUpper', function(text){
    return text.charAt(0).toUpperCase() + text.slice(1);
  });

  Vue.filter('myDate', function(created){
    return moment(created).format('MMMM Do YYYY');
  });

  //laravel Passport
  Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);
  
  //Custom global event 
  const eventBus = new Vue();
  window.eventBus = eventBus;
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    router,
    data: {
      search: ''
    },
    methods: {
      searchit() {
        eventBus.$emit('seacrh');      
      }
    }


}).$mount('#app');
