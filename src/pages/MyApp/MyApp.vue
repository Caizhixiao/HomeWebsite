<template>
  <div>
    <h2 style="margin-top:1%;" v-show="!InApp">应用列表:</h2>
    <a-list :grid="{ gutter: 16, xs: 1, sm: 2, md: 4, lg: 4, xl: 6, xxl: 3 }" :data-source="outputData" v-show="!InApp">
      <a-list-item slot="renderItem" slot-scope="item">
        <a-card :title="item.title" :key="item.id">
          {{item.text}}<br/>
          <router-link
              :to="{path: '/app/'+item.id}"
              :disabled="!item.available"
              tag="a-button"
              class="ant-btn ant-btn-primary"
          >
            前往
          </router-link>
        </a-card>
      </a-list-item>
    </a-list>
    <router-view></router-view>
  </div>

</template>
<script>
const data = [
  {
    title: '药品过期提醒',
    text: '',
    id:'8yuzxcew97',
    available:true,
    requireAccess: 1,
  },
  {
    title: '档案管理',
    text: '用于集中管理医院记录',
    id:'vcx978cxz23',
    available:false,
    requireAccess: 1,
  },
  {
    title: '网站运行/开发日志审计',
    text: '',
    id:'cxz978qwi9e',
    available: true,
    requireAccess: 3,
  }
];

export default {
  data() {
    return {
      data,
    };
  },
  computed: {
    InApp(){
      return this.$route.path.startsWith('/app/');
    },
    outputData(){
      return this.data.filter((item) => {
        return this.$store.state.UserOptions.users.accessN >= item.requireAccess && item.available;
      });
    }
  },
};
</script>
<style></style>