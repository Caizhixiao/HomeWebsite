<template>
  <div style="margin-top:8%;align-content: center;justify-content: center;width: 40%;margin-left: 30%;" class="mainContainer">
    <a-result
        status="success"
        title="登出成功"
        :sub-title="subTitle"
    >
      <template #extra>
      </template>
    </a-result>
  </div>
</template>

<script>
import {mixin} from '@/mixin.js';
export default {
  name:'UserLogout',
  data(){
    return {
      subTitle: '1s后自动跳转至首页',
    }
  },
  mixins:[mixin],
  methods:{
    goIndex(){
      this.$router.replace('/login');
    }
  },
  mounted() {
    this.clearCookies();
    this.$store.state.UserOptions.users = {
      id:'',
      token:'',
      access:'',
      username:'未登录',
      accessN:'',
    }
    let countdown = 1;
    let timer = setInterval(()=>{
      countdown--;
      this.subTitle = countdown+"s后自动跳转至首页";
    },1000);
    setTimeout(()=>{
      clearInterval(timer);
      this.goIndex();
    },1000);
  }
}
</script>

<style scoped>

</style>