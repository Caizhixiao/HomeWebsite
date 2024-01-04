<template>
  <div style="margin-top:8%;align-content: center;justify-content: center;width: 40%;margin-left: 30%;" class="mainContainer">
  <a-result
      status="error"
      title="没有权限访问"
      sub-title="请检查您是否登录/您是否有权限访问此页面."
  >
    <template #extra>
      <a-button key="console" type="primary" @click="goLogin">
        前去登录
      </a-button>
    </template>

    <div class="desc">
      <p style="font-size: 16px;">
        <strong>可能的问题:</strong>
      </p>
      <p>
        <a-icon :style="{ color: 'red' }" type="close-circle" /> 您尚未登录
        <a @click="goLogin()">前去登录&gt;</a>
      </p>
      <p>
        <a-icon :style="{ color: 'red' }" type="close-circle" /> 您账户的权限不足，请联系管理员，提升权限
<!--        <a>Apply Unlock &gt;</a>-->
      </p>
      <p>
        <a-icon :style="{ color: 'red' }" type="close-circle" /> 网络波动，您的账户验证请求未能到达服务器
        <!--        <a>Apply Unlock &gt;</a>-->
      </p>
    </div>
  </a-result>
  </div>
</template>
<script>
export default {
  name: 'NoAccess',
  data() {
    return {};
  },
  methods: {
    goLogin() {
      this.$router.push('/');
    },
  },
};
</script>
<style scoped>
.desc p {
  margin-bottom: 1em;
}
</style>
