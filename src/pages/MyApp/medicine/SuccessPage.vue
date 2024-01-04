<template>
  <div style="margin-top:8%;align-content: center;justify-content: center;width: 40%;margin-left: 30%;" class="mainContainer">
    <a-result
        status="success"
        title="请求成功"
        sub-title="您的请求已经成功处理"
    >
      <template #extra>
        <a-button key="console" type="primary" @click="goIndex">
          前去主页
        </a-button>
      </template>
    </a-result>
  </div>
</template>

<script>
  export default {
    name:'SuccessPage',
    methods:{
      goIndex(){
        this.$bus.$emit('quitDetails',true);
        this.$router.replace({name: 'MedicineList'});
      }
    }
  }
</script>

<style scoped>

</style>