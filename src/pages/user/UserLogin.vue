<template>
  <div>
    <a-result title="您已登录，无需再次登录!" v-show="$store.state.UserOptions.users.accessN" class="">
      <template #extra>

      </template>
    </a-result>
  <div class="loginform" v-show="!$store.state.UserOptions.users.accessN">
    <h1 style="text-align: center;">登录</h1>
  <a-form
      id="components-form-demo-normal-login"
      :form="form"
      class="login-form"
      @submit="handleSubmit"
  >
    <a-form-item>
      <a-input
          v-decorator="[
          'username',
          { rules: [{ required: true, message: '用户名不能为空' }] },
        ]"
          placeholder="用户名"
      >
        <a-icon slot="prefix" type="user" style="color: rgba(0,0,0,.25)" />
      </a-input>
    </a-form-item>
    <a-form-item>
      <a-input
          v-decorator="[
          'password',
          { rules: [{ required: true, message: '密码不能为空' }] },
        ]"
          type="password"
          placeholder="密码"
      >
        <a-icon slot="prefix" type="lock" style="color: rgba(0,0,0,.25)" />
      </a-input>
    </a-form-item>
    <a-form-item>
      <a-checkbox
          v-decorator="[
          'remember',
          {
            valuePropName: 'checked',
            initialValue: true,
          },
        ]"
      >
        Remember me
      </a-checkbox>
      <a-button type="primary" html-type="submit" class="login-form-button" :loading="isLoading">
        登录
      </a-button><br/><br/>
    </a-form-item>
  </a-form>
  </div>
  </div>
</template>

<script>

import axios from "axios";
import {mixin} from "../../mixin.js";
export default {
  name: 'UserLogin',
  data(){
    return {
      isLoading: false,
    }
  },
  mixins: [mixin],
  computed:{
    isLogin(){
      return this.$store.state.UserOptions.users.accessN !== 0;
    }
  },

  beforeCreate() {
    this.form = this.$form.createForm(this, { name: 'normal_login' });
  },
  methods: {
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields((err, values) => {
        if (!err) {
          const formData = new FormData();
          formData.append('username', values.username);
          formData.append('password', values.password);
          this.isLoading = true;
          axios.post('https://home.caizhixiao.com.cn/Api/Account/login.php',formData)
              .then((response)=> {
                this.isLoading = false;
                let data = response.data;
                if (data.code === 200){
                  data = {
                    ...data,
                    username:values.username,
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
                  this.$router.push({
                    path: '/blocked',
                    query: {
                      username: values.username,
                      password: values.password,
                      msg: data.text,
                    }
                  });
                }
                else if (data.code === 0){
                  this.$router.push({
                    path: '/blockIP'
                  });
                }
                else{
                  this.$message.error(data.text);
                }
              })
              .catch((err)=>{
                this.$message.error(err);
              })

        }
      });
    },
  },
  mounted() {
    if (this.$store.state.UserOptions.users.accessN){
      setTimeout(() => {
        this.$router.push('/app');
      },100);
    }
  }
};
</script>
<style scoped>
#components-form-demo-normal-login .login-form {
  max-width: 300px;
}
#components-form-demo-normal-login .login-form-forgot {
  float: right;
}
#components-form-demo-normal-login .login-form-button {
  width: 100%;
}

.loginform{
  width:25%;

  justify-content: center;
  align-items: center;
  margin-top: 15%;
  margin-left: 38%;
  box-shadow:  2px 2px 2px #ddd;
  border-radius: 2%;
}
.login-form{
  width: 95%;
  margin-top: 3%;
}
</style>