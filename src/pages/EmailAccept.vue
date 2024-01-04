<template>
  <a-result
      status="success"
      title="账号已解锁"
      sub-title="您的账号已解锁，您可以重新登录."
  >
  </a-result>
</template>
<script>
import axios from "axios";
export default {
  name: 'EmailAccept',
  data() {
    return {};
  },
  mounted() {
    const sendFormdata = new FormData();
    sendFormdata.append('BlockID', this.$route.params.id);
    sendFormdata.append('RequestType', 'approve');
    axios.post('https://home.caizhixiao.com.cn/Api/Account/blockRequest.php',sendFormdata).then(response =>{
          console.log(response);
        },
        error => {
          this.$message.error(error);
        })
  }
};
</script>

<style scoped>

</style>