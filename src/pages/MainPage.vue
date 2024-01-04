<template>
  <div>
    <h2 style="float: left;margin-right: 2%;margin-top: 0.3%;margin-left: 1%;" v-show="show">HToolBox</h2>
    <a-menu v-model="current" mode="horizontal" v-show="show">
      <a-menu-item key="home" v-if="isUser">
        <router-link to="/home" ><a-icon type="home" />主页</router-link>
      </a-menu-item>

      <a-menu-item key="app" v-if="isUser">
        <router-link to="/app" ><a-icon type="appstore" />应用</router-link>
      </a-menu-item>
      <a-menu-item key="control" v-if="isAdmin"> <a-icon type="control" />后台管理 </a-menu-item>
      <a-sub-menu style="float: right">
        <span slot="title" class="submenu-title-wrapper"
        ><a-icon type="user" />{{$store.state.UserOptions.users.username}}</span>
        <a-menu-item-group title="操作">
          <a-menu-item v-if="acc === 0" key="login">
            登录
          </a-menu-item>
          <a-menu-item v-if="isUser" key="personal_center">
            个人中心
          </a-menu-item>
          <a-menu-item v-if="isUser" key="settings">
            设置
          </a-menu-item>
          <a-menu-item v-if="isUser" key="logout">
            <router-link to="/logout">退出登录</router-link>
          </a-menu-item>
        </a-menu-item-group>
      </a-sub-menu>
    </a-menu>
    <router-view></router-view>
  </div>
</template>
<script>
import {mixin} from "../mixin.js";
let accessChecker;
export default {
  name: 'HomeBanner',
  components: {},
  data() {
    return {
      current: ['login'],
    };
  },
  mixins: [mixin],
  computed: {
    acc(){
      return this.$store.state.UserOptions.users.accessN || 0;
    },
    isUser(){
      return this.$store.state.UserOptions.users.accessN >= 1;
    },
    isAdmin(){
      return this.$store.state.UserOptions.users.accessN >= 2;
    },
    show(){
      return this.$route.path.indexOf('/AskDecision') === -1;
    }
  },
  mounted() {
    this.$store.dispatch('UserOptions/UserAccess',{userid: this.getCookie('userid'),accessToken: this.getCookie('accessToken')}).then(() => {
      if (this.$store.state.UserOptions.users.accessN === -1){
        window.location.href='/BlockIP';
        clearInterval(accessChecker);
      }
    });
    accessChecker = setInterval(() => {
      this.$store.dispatch('UserOptions/UserAccess',{userid: this.getCookie('userid'),accessToken: this.getCookie('accessToken')}).then(() => {
        if (this.$store.state.UserOptions.users.accessN === -1){
          window.location.href='/BlockIP';
          clearInterval(accessChecker);
        }
      });
    }, 1000 * 60 * 20);//20min检查一次
  },
  beforeDestroy() {
    clearInterval(accessChecker);
  },
};
</script>

<style scoped>

</style>