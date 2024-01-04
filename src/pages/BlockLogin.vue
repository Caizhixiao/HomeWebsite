<script>
  import axios from "axios";
  import {mixin} from "@/mixin.js";
  export default {
    name: 'BlockLogin',
    data() {
      return {};
    },
    mixins:[mixin],
    methods: {
      checkLogin(){
        const sendFormdata = new FormData();
        sendFormdata.append('username', this.$route.query.username);
        sendFormdata.append('password', this.$route.query.password);
        axios.post('https://home.caizhixiao.com.cn/Api/Account/login.php',sendFormdata).then(response =>{
              let data = response.data;
              if (data.code === 200){
                data = {
                  ...data,
                  username:this.$route.query.username,
                }
                this.$store.commit('UserOptions/LOGIN',data);
                this.$store.dispatch('UserOptions/UserAccess',{userid:data.userid,accessToken:data.accessToken});
                this.$message.success('登录成功');
                this.setCookie('token',data.token,0.5);
                this.setCookie('userid',data.userid,0.5);
                this.setCookie('accessToken',data.accessToken,0.5);
                this.setCookie('username',data.username,0.5);
                setTimeout(() => {
                  this.$router.push('/app');
                },500);
              }
              else if (data.code === 900){
                this.$message.error("您的账户尚未解除安全警报，无法登录!")
              }
              else if (data.code === 0){
                this.$router.push({
                  path: '/blockIP'
                });
              }
              else{
                this.$message.error(data.text);
              }
            },
            error => {
              this.$message.error(error);
            })
      }
    },
  };
</script>

<template>
  <a-result title="您的账户已触发安全警报"  class="" style="margin-top: 10%;">
    <template #extra>
      {{"我们"+$route.query['msg']+"，请到邮箱中确认操作!"}}
      <br/><br/><br/>
      <a-button type="primary" @click="checkLogin()">
        检查
      </a-button>
    </template>
  </a-result>
</template>

<style scoped>

</style>