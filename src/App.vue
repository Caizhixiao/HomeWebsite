<template>
  <div id="app">
    <MainPage/>
  </div>
</template>

<script>
import MainPage from "@/pages/MainPage.vue";
import {mixin} from "./mixin.js";
export default {
  name: 'App',
  components: {
    MainPage,
  },
  mixins: [mixin],
  mounted() {
    console.log("App.vue mounted");
    this.$bus.$on('getLoginMsg', (data) => {
      if (data !== "SUCCESS"){
        this.$message.error(data);
      }
    });
    this.$bus.$on('Msg', (data) => {
      if (data.type === "error")
        this.$message.error(data.text);
      else if (data.type === "success")
        this.$message.success(data.text);
      else if (data.type === "warning")
        this.$message.warning(data.text);
      else if (data.type === "info")
        this.$message.info(data.text);
    });
    if (this.getCookie('token') !== null){
      let user_data = {
        token:this.getCookie('token'),
        userid:this.getCookie('userid'),
        accessToken:this.getCookie('accessToken'),
        username:this.getCookie('username'),
      }
      this.$store.commit('UserOptions/LOGIN',user_data);
    }
    this.$store.dispatch('UserOptions/UserAccess',
        {
          userid:this.getCookie('userid'),
          accessToken:this.getCookie('accessToken')
        }
    );
  }
}
</script>

<style>
body{
  background-image: url('./assets/background.svg');
}
</style>
