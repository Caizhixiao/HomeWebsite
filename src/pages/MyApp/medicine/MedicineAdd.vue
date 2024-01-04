<template>
  <div>
    <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        title="添加药品"
        sub-title=""
        @back="() => {
          this.$bus.$emit('quitDetails',true);
          this.$router.go(-1);
        }"
    />
    <a-form-model
        ref="ruleForm"
        :model="form"
        :rules="rules"
        :label-col="labelCol"
        :wrapper-col="wrapperCol"
    >
      <br/><br/>
      <a-form-model-item ref="creatorID" label="创建者ID">
        <a-input
            v-model="form.creator"
            disabled="true"

        />
      </a-form-model-item>
      <a-form-model-item ref="name" label="药品名称" prop="name">
        <a-input
            v-model="form.name"
            @blur="
          () => {
            $refs.name.onFieldBlur();
          }
        "
        />
      </a-form-model-item>
      <a-form-model-item label="拥有数量(单位)" prop="have">
      <a-input-number id="have" v-model="form.have" :min="0" :max="10000" :step="0.1" @change="onChange"/>
      </a-form-model-item>
      <a-form-model-item ref="room" label="科室" prop="room">
        <a-input
            v-model="form.room"
            @blur="
          () => {
            $refs.room.onFieldBlur();
          }
        "
        />
      </a-form-model-item>
      <a-form-model-item label="所属人">
        <a-select v-model="form.BelongTo" placeholder="请选择药品的所属人">
          <a-select-option v-for="(l,index) in name_listo" :value="l" :key="index">
            {{l}}
          </a-select-option>
        </a-select>
      </a-form-model-item>
      <a-form-model-item label="药品用法" prop="des1c">
        一天<a-input-number id="usage_times" v-model="form.usage.times" :min="1" :max="10" @change="onChange" size="small"/> 次,
        一次<a-input-number id="usage_once" v-model="form.usage.once" :min="0.1" :max="10"  :step="0.1"  @change="onChange" size="small"/>个(单位)
      </a-form-model-item>
      <a-form-model-item label="状态">
        <a-switch v-model="form.open" />
      </a-form-model-item>
      <a-form-model-item label="描述" prop="desc">
        <a-input v-model="form.desc" type="textarea" />
      </a-form-model-item>
      <a-form-model-item :wrapper-col="{ span: 14, offset: 4 }">
        <a-button type="primary" @click="onSubmit">
          提交
        </a-button>
        <a-button style="margin-left: 10px;" @click="resetForm">
          重置
        </a-button>
      </a-form-model-item>
    </a-form-model>
  </div>
</template>
<script>
import axios from "axios";

export default {
  data() {

    return {
      labelCol: { span: 4 },
      wrapperCol: { span: 14 },
      other: '',
      name_list:[],
      form: {
        name: '',
        desc: '',
        room: '',
        stat: -1,
        open: true,
        endby:0,
        creator:this.$store.state.UserOptions.users.id,
        belongTo:undefined,
        have:0,
        usage:{
          times:1,
          once:1
        },
      },
      rules: {
        name: [
          { required: true, message: '请输入药品名称', trigger: 'blur' },
          { min: 2, max: 12, message: '长度必须为2~12位', trigger: 'blur' },
        ],
      },
    };
  },
  methods: {
    onSubmit() {
      this.$refs.ruleForm.validate(valid => {
        if (valid) {
          let duse = this.form.usage.once * this.form.usage.times;
          let can_use  = Math.floor(this.form.have / duse);
          if (this.form.open === false){
            this.form.stat = can_use;
          }
          else{
            let endby = new Date();
            endby.setDate(endby.getDate() + can_use);
            this.form.endby = endby;
          }
          const sendData = new FormData();
          sendData.append('token', this.$store.state.UserOptions.users.token);
          sendData.append('userid', this.$store.state.UserOptions.users.id);
          sendData.append('accessToken', this.$store.state.UserOptions.users.access);
          sendData.append('data', JSON.stringify(this.form));
          axios.post('https://home.caizhixiao.com.cn/Api/App/User/8yuzxcew97/add.php',sendData).then(
              (res) => {
                if (res.data.code === 200){
                  this.$message.success(res.data.text);
                  this.$router.replace({name: 'SuccessPage'});
                }
                else{
                  this.$message.error(res.data.text);
                }
              },
              (err) => {
                this.$message.error(err);
              }
          )
        } else {
          console.log("ERR");
          return false;
        }
      });
    },
    resetForm() {
      this.$refs.ruleForm.resetFields();
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

    }
  },
  computed:{
    name_listo() {
       return this.name_list.filter((t) => {
         return t.indexOf("testAccount") === -1;
       });
    }
  },
  mounted() {
    setTimeout(() => {
      this.getNameList();
      if (this.form.creator === undefined){
        this.$message.error("无法获取UID，请刷新重试!");
      }
    },100);
  }
};
</script>

<style scoped>

</style>