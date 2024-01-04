<template>
  <div style="margin-top:8%;align-content: center;justify-content: center;width: 40%;margin-left: 30%;" class="mainContainer">
    <a-result
        status="error"
        title="您的IP已被封禁"
        sub-title="请联系管理员"
    >
      <template #extra>
      </template>

      <div class="desc">
        <p style="font-size: 16px;">
          <strong>可能的问题:</strong>
        </p>
        <p>
          <a-icon :style="{ color: 'red' }" type="close-circle" /> 您曾尝试登录他人账号
        </p>
        <p>
          <a-icon :style="{ color: 'red' }" type="close-circle" /> 您所在的IP段有人恶意登录/攻击，请联系管理员解封
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
  },
};
</script>
<style scoped>
.desc p {
  margin-bottom: 1em;
}
</style>
