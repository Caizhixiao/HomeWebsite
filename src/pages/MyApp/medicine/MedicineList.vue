<template>
  <div>
    <a-spin :spinning="spinning" :delay="delayTime">
    <div class="tableContainer" v-show="!InOtherPage">
    <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        :title="AppName"
        sub-title=""
        @back="() => {
          this.$router.push({name: 'MyApp'});
        }"
    />
    <br/><br/>
    <div class="mainBody" style="width: 96%;justify-content: center;align-items: center;margin-left:2%;margin-top:2%;">
      <a-button type="primary" @click="InOtherPage = true;$router.push({name: 'MedicineAdd'})">
        <a-icon type="plus-circle"/>
        添加
      </a-button>
      <a-select
          mode="multiple"
          :size="size"
          placeholder="按名字检索"
          style="width: 150px;float: right;"
          @change="handleChange"
          @popupScroll="popupScroll"
      >
        <a-icon slot="suffixIcon" type="search" />
        <a-select-option v-for="(l,index) in name_listo" :key="index">
          {{l}}
        </a-select-option>
      </a-select>
      <br/><br/><br/>
      <template>
        <a-table
            :columns="columns"
            :data-source="data"
            :pagination="{ pageSize: 10 }"
            :rowKey="(record) => {return record.id}"
            v-show="!spinning"
        >
          <span slot="endby" slot-scope="text,record">
            <a-tag
                :color="
                record.stat >= 0 ? 'purple' :
                (record.endby > 14 ? 'green' :
                (record.endby > 7 ? 'blue' :
                (record.endby === 0 ? 'red' : 'orange')))"
            >
            {{record.stat >= 0 ? record.stat : record.endby}}{{record.stat >= 0 ? '天(停用)' : '天'}}
            </a-tag>
          </span>
          <span slot="operation" slot-scope="text,record">
            <a-button type="link" @click="() => {details(record.id)}">详情</a-button>
          </span>
        </a-table>
      </template>
    </div>
    </div>
    </a-spin>
    <router-view v-show="InOtherPage" :key="$route.fullPath"></router-view>
  </div>
</template>
<script>
import axios from 'axios';
import dayjs from 'dayjs';
const columns = [
  {
    title: '药品名称',
    dataIndex: 'name',
    width: '30%',
    sorter: (a, b) => a.name.length - b.name.length,
  },
  {
    title: '科室',
    dataIndex: 'room',
    width: '20%',
    sorter: (a, b) => a.room.length - b.room.length,
  },
  {
    title: '剩余',
    dataIndex: 'endby',
    width: '20%',
    scopedSlots: { customRender: 'endby' },
    defaultSortOrder: 'ascend',
    sorter: (a, b) => {
      const INF = 99999999;
      let aend = (a.stat >=0 ? INF + a.stat : a.endby);
      let bend = (b.stat >=0 ? INF + b.stat : b.endby);
      return aend - bend;
    },
  },
  {
    title: '属于',
    dataIndex: 'BelongTo',
    width: '20%',
    sorter: (a, b) => a.BelongTo.length - b.BelongTo.length,
  },
  {
    title: '操作',
    dataIndex: 'operation',
    width:'10%',
    scopedSlots: { customRender: 'operation' },
  },
];
export default {
  data() {
    return {
      AppName: '药品过期提醒',
      columns,
      size:'default',
      name_list:[],
      InOtherPage:false,
      selected:'',
      data:[],
      spinning: true,
      delayTime: 10,
    };
  },
  methods:{
    handleChange(value) {
      let str = "";
      for (let i = 0; i < value.length; i++) {
        str += this.name_listo[value[i]] + ",";
      }
      this.selected = str;
      this.getData(this.selected);
    },
    popupScroll() {
      console.log('popupScroll');
    },
    getData(value){
      const sendData = new FormData();
      sendData.append('token', this.$store.state.UserOptions.users.token);
      sendData.append('userid', this.$store.state.UserOptions.users.id);
      sendData.append('accessToken', this.$store.state.UserOptions.users.access);
      sendData.append('names', value);
      this.data = [];
      axios.post('https://home.caizhixiao.com.cn/Api/App/User/8yuzxcew97/get.php',sendData).then((res)=>{
        const val = res.data.result;
        Object.freeze(val);
        if (res.data.code !== 200){
          this.$message.error(res.data.text);
          return ;
        }
        let v = [];
        for (let i in val){
          v.push(val[i].BelongTo);//i * 2
          v.push(val[i].creator);//i * 2 + 1
        }
        let sub = '';
        for (let i = 0;i < v.length ; i++){
          if (i !== 0) sub += ',';
          sub += v[i];
        }
        const formData = new FormData();
        formData.append('userid', this.$store.state.UserOptions.users.id);
        formData.append('token', this.$store.state.UserOptions.users.token);
        formData.append('accessToken', this.$store.state.UserOptions.users.access);
        formData.append('findUserID',sub);
        axios.post('https://home.caizhixiao.com.cn/Api/Account/findUserID.php',formData).then(
            response =>{
              if (response.data.code === 200){
                for (let i = 0; i < val.length ; i++){
                  let result = val[i];
                  result.BelongTo = response.data.result[i * 2];
                  result.creator = response.data.result[i * 2 + 1];
                  result.creatorID = v[i * 2 + 1];
                  result.usage = JSON.parse(result.usage);
                  result.LastUpdateTime = dayjs(result.LastUpdateTime * 1000).format('YYYY-MM-DD HH:mm:ss');
                  result.endbyT = new Date(result.endby);
                  let lft = new Date(result.endby) - new Date();
                  lft = lft / 24 / 60 / 60 / 1000;
                  lft = lft.toFixed(1);
                  result.endby = (lft > 0 ? lft : 0);
                  this.data.push(result);
                }
              }else{
                this.$message.error(response.data.result);
                return response.data.result;
              }
            },
            (err)=>{
              this.$message.error(err);
              return err;
            }
        );
       });
    },
    getNameList(){
      const sendData = new FormData();
      sendData.append('token', this.$store.state.UserOptions.users.token);
      sendData.append('userid', this.$store.state.UserOptions.users.id);
      sendData.append('accessToken', this.$store.state.UserOptions.users.access);
      axios.post('https://home.caizhixiao.com.cn/Api/Account/getUserName.php',sendData).then((res)=>{
        this.name_list = res.data.result;
        return res.data.code === 200 ? res : [];
      },(err)=>{
        this.$message.error(err);
      });
    },
    search(){
      let str = "";
      for (let i = 0; i < this.name_list.length; i++) {
        str += this.name_list[i] + ",";
      }
      this.getData(str);
    },
    details(key){
      for (let i = 0; i< this.data.length ; i++){
        if (this.data[i].id === key){
          this.$store.commit('MedicineOptions/COMMIT',{
            ...this.data[i],
          });
          this.InOtherPage = true;
          this.$router.push({name:'MedicineDetails'});
          break;
        }
      }
    }
  },
  computed:{
    name_listo(){
      return this.name_list.filter((t) => {
        return t.indexOf("testAccount") === -1;
      })
    }
  },
  mounted() {

    setTimeout(() => {
      this.getNameList();
    },100);
    setTimeout(() =>{
      this.spinning = !this.spinning;
    },1000);
    this.$bus.$on('quitDetails',(data) => {
      this.InOtherPage = !data;
      this.getNameList();
      this.getData(this.selected);
    });
    this.$bus.$on('MedQuestErrMsg',(data) => {
      this.$message.error(data);
    });
  },
};
</script>


<style scoped>

</style>