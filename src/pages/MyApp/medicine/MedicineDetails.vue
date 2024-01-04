<template>
  <div style="background-color: #F5F5F5; padding: 24px">
    <a-page-header title="药品详情" :sub-title="'创建者:'+item.creator" @back="() => {
      this.$bus.$emit('quitDetails',true);
      this.$router.go(-1);
    }" :ghost="false">
      <template slot="tags">
        <a-tag :color="item.stat === '-1' ? 'blue' : 'red'">
          {{item.stat === '-1' ? '启用' : '停用'}}
        </a-tag>
      </template>
      <template slot="extra">
        <a-button key="1" type="primary" v-show="!isEditingMode" @click="edit_use">
          <a-icon type="edit"/>编辑
        </a-button>
        <a-button key="2" type="danger" v-show="!isEditingMode" @click="delete_item">
          <a-icon type="delete"/>删除
        </a-button>
        <a-button key="3" type="primary" v-show="isEditingMode">
          <a-icon type="check-circle"/>确认
        </a-button>
        <a-button key="4" type="danger" v-show="isEditingMode" @click="quit_edit">
          <a-icon type="close-circle"/>取消
        </a-button>
      </template>
      <a-row type="flex">
        <a-statistic title="药品名称" :value="item.name" />
        <a-statistic
            title="剩余(天)"
            :value="item.stat >= 0 ? item.stat : item.endby"
            :style="{
            margin: '0 32px',
          }"
        />
        <a-statistic title="科室"  :value="item.room"/>
        <a-statistic title="属于"  :value="item.BelongTo" :style="{margin: '0 32px'}"/>
      </a-row>
<!--      <a-collapse v-model="activeKey" :expand-icon-position="expandIconPosition" style="margin-top: 5%;">-->
<!--        <a-collapse-panel key="1" header="基础信息">-->
<!--          <p><strong class="title">药品名称:</strong>{{ item.name }}</p>-->
<!--          <p><strong class="title">创建者:</strong>{{ item.creator }}</p>-->
<!--          <p><strong class="title">剩余天数:</strong>-->
<!--            <a-tag-->
<!--                :color="-->
<!--                item.stat >= 0 ? 'purple' :-->
<!--                (item.endby > 14 ? 'green' :-->
<!--                (item.endby > 7 ? 'blue' :-->
<!--                (item.endby === 0 ? 'red' : 'orange')))"-->
<!--            >-->
<!--              {{item.stat >= 0 ? item.stat : item.endby}}{{item.stat >= 0 ? '天(停用)' : '天'}}-->
<!--            </a-tag>-->
<!--          </p>-->
<!--          <p><strong>用尽日期:</strong>{{endbyT_format}}</p>-->
<!--          <p><strong class="title">状态:</strong>-->
<!--            <a-tag :color="item.stat === '-1' ? 'blue' : 'red'">-->
<!--              {{item.stat === '-1' ? '启用' : '停用'}}-->
<!--            </a-tag>-->
<!--          </p>-->
<!--          <p><strong class="title">药品用法:</strong>{{-->
<!--              "一次" + item.usage.once + "个(单位) , " +-->
<!--              "一天" + item.usage.times + "次"-->
<!--                  }}-->
<!--          </p>-->
<!--          <p><strong class="title">所属科室:</strong>{{ item.room }}</p>-->
<!--          <p><strong class="title">上次更新:</strong>{{ LastUpdateTime_format }}</p>-->
<!--          <p><strong class="title">药品描述:</strong><br/>&nbsp;&nbsp;{{ item.description }}</p>-->
<!--          <a-icon slot="extra" type="setting" @click="handleClick" />-->
<!--        </a-collapse-panel>-->
<!--        <a-collapse-panel key="2" header="更新日志" disabled>-->
<!--          <p>{{ 'text' }}</p>-->
<!--          <a-icon slot="extra" type="setting" @click="handleClick" />-->
<!--        </a-collapse-panel>-->
<!--      </a-collapse>-->
      <br/><br/><br/><br/><br/>
      <a-descriptions title="" bordered>
        <a-descriptions-item label="名称">
          {{ item.name }}
        </a-descriptions-item>
        <a-descriptions-item label="科室">
          {{ item.room }}
        </a-descriptions-item>
        <a-descriptions-item label="所属人">
          {{ item.BelongTo }}
        </a-descriptions-item>
        <a-descriptions-item label="剩余天数">
          {{item.stat >= 0 ? item.stat : item.endby}}{{item.stat >= 0 ? '天(停用)' : '天'}}
        </a-descriptions-item>
        <a-descriptions-item label="用尽日期" :span="2">
          {{item.stat >= 0 ? '' : endbyT_format}}
        </a-descriptions-item>
        <a-descriptions-item label="状态" :span="3">
          <a-badge :status="
            item.stat >= 0 ? 'default' :
                (item.endby > 14 ? 'success' :
                (item.endby > 7 ? 'processing' :
                (item.endby === 0 ? 'error' : 'warning')))
            "
                   :text="
              item.stat >= 0 ? '停用' :
                (item.endby > 14 ? '安全' :
                (item.endby > 7 ? '警告' :
                (item.endby === 0 ? '用尽' : '危险')))
            " />
        </a-descriptions-item>
        <a-descriptions-item label="用法用量" :span="2">
          {{"一次" + item.usage.once + "个(单位) , " +
        "一天" + item.usage.times + "次"
          }}
        </a-descriptions-item>
        <a-descriptions-item label="上次更新" :span="3">
          {{ LastUpdateTime_format }}
        </a-descriptions-item>
        <a-descriptions-item label="描述/备注" :span="3">
          {{item.description}}
          <a-empty v-show="item.description === ''"/>
          <div v-show="item.description !== ''"><br/><br/><br/><br/><br/></div>
        </a-descriptions-item>
        <a-descriptions-item label="更新日志" :span="3">
          <a-empty/>
        </a-descriptions-item>
      </a-descriptions>
      <br/><br/>
    </a-page-header>

  </div>
</template>

<script>
import dayjs from 'dayjs';
import axios from 'axios';
  export default{
    name:'MedicineDetails',
    data(){
      return{
        activeKey: ['1'],
        expandIconPosition: 'right',
        isEditingMode: false,
        delAlertVisible: false,
        button:{
          text: '我确定(5s后可用)',
          disabled: true,
        }
      }
    },
    methods:{
      handleClick(event) {
        // If you don't want click extra trigger collapse, you can prevent this:
        event.stopPropagation();
      },
      edit_use(){
        this.isEditingMode = true;
        this.$router.push({name:'MedicineEdit'});
      },
      quit_edit(){
        let vc = this;
        this.$confirm({
          title: '确定退出编辑模式?',
          content: '你的所有更改将不会被保存!',
          okText: '我确定',
          okType: 'danger',
          cancelText: '再想想',
          onOk() {
            vc.isEditingMode = false;
          },
          onCancel() {

          },
        });
      },
      delete_item(){
        let vc = this;
        this.$confirm({
          title: '确认删除这条药品信息?',
          content: '删除操作无法撤销',
          okText: '我确定',
          okType: 'danger',
          cancelText: '再想想',
          onOk() {
            const formData = new FormData();
            formData.append('medid',vc.item.id);
            formData.append('userid',vc.$store.state.UserOptions.users.id);
            formData.append('token',vc.$store.state.UserOptions.users.token);
            formData.append('accessToken',vc.$store.state.UserOptions.users.access);
            axios.post('https://home.caizhixiao.com.cn/Api/App/User/8yuzxcew97/dlt.php',formData).then((res)=>{
              console.log(res.data);
              if(res.data.code === 200){
                vc.$message.success('删除成功');
                vc.$bus.$emit('quitDetails',true);
                setTimeout(() => {
                  vc.$router.go(-1);
                },100);
              }else{
                vc.$message.error('删除失败,'+res.data.text);
              }
            }).catch((err)=>{
              vc.$message.error('删除失败,'+err);
            });
          },
          onCancel() {
          },
        });
      },
    },
    computed:{
      item(){
        return this.$store.state.MedicineOptions.item;
      },
      endbyT_format(){
        return dayjs(this.item.endbyT).format('YYYY-MM-DD HH:mm:ss');
      },
      LastUpdateTime_format(){
        return dayjs(this.item.LastUpdateTime).format('YYYY-MM-DD HH:mm:ss');
      }
    },
    mounted(){
    //  console.log("MedicineDetails");
    }
  }
</script>

<style scoped>
tr:last-child td {
  padding-bottom: 0;
}
.title {
  font-weight: 600;
  padding-right: 1.5px;
}
</style>