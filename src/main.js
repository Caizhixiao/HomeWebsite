import Vue from 'vue';
import App from './App.vue';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import store from './store';
import VueRouter from "vue-router";
import router from './router/index.js';

Vue.config.productionTip = false
Vue.use(Antd);
Vue.use(VueRouter);
 new Vue({
  el:'#app',
  render: h => h(App),
  store,
  router,
  beforeCreate() {
    Vue.prototype.$bus = this;
  }
});
