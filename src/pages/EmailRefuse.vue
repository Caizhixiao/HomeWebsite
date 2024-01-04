<template>
  <a-result
      status="warning"
      title="请求的IP已加入黑名单"
      sub-title="您已拒绝了此次请求，请求的IP地址已被加入黑名单."
  >
  </a-result>
</template>
<script>
import axios from "axios";
export default {
  name: 'EmailRefuese',
  data() {
    return {};
  },
  mounted() {
    const sendFormdata = new FormData();
    sendFormdata.append('BlockID', this.$route.params.id);
    sendFormdata.append('RequestType', 'refused');
    axios.post('https://home.caizhixiao.com.cn/Api/Account/blockRequest.php',sendFormdata).then(response =>{
      console.log(response);
    },
    error => {
     this.$message.error(error);
    });
  }
};
</script>

<style scoped>

</style>