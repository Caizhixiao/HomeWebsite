<template>
  <div>
    <a-result status="404" title="询问" :sub-title="msg + '。是否为您本人操作'" v-show="err === 0">
      <template #extra>
        <a-button type="primary" size="large" @click="$router.replace('/email_accepted/'+$route.params.id)">是</a-button>
        &nbsp;&nbsp;&nbsp;
        <a-button type="danger" size="large" @click="$router.replace('/email_refused/'+$route.params.id)">否</a-button>
      </template>
    </a-result>
    <a-result status="warning" title="询问不存在" sub-title="该询问不存在或已被回答" v-show="err === 1">
    </a-result>
    <a-result status="error" title="错误" sub-title="路径中包含非法字符" v-show="err === 2">
    </a-result>
  </div>
</template>
<script>
import axios from 'axios';
const urlParams = new URLSearchParams(window.location.search);
  export default {
    name: 'AskDecision',
    data(){
      return {
        msg:urlParams.get('msg'),
        err: 0,
      }
    },
    mounted() {
      axios.get('https://home.caizhixiao.com.cn/Api/Account/ExisitAsking.php?ID='+this.$route.params.id).then(
          res => {
            switch (res.data.code) {
              case 200:
                break;
              case -200:
                this.err = 1;
                break;
              case 0:
                this.$router.replace('/BlockIP');
                break;
              case -403:
                this.err = 2;
                break;
              default:
                break;
            }
          }
      )
    }
  }
</script>

<style scoped>

</style>