<template>
  <div>
    <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        title="编辑药品"
        sub-title=""
        @back="() => {
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
        <span>{{form.have}}</span>
        <a-icon type="plus" style="margin-left: 0.5%;margin-right: 0.5%;" v-show="inEditingMode"/>
        <a-input-number
            :min="-1000000"
            :max="1000000"
            :step="0.1"
            v-model="addNumber"
            v-show="inEditingMode"
        />
        &nbsp;&nbsp;
        <a-button
            type="primary"
            shape="circle"
            icon="edit"
            size="small"
            style="margin-left: 0.5%;"
            v-show="!inEditingMode"
            @click="inEditingMode = true;addNumber = 0;"
        />
        <a-button
            type="default"
            shape="circle"
            icon="close"
            size="small"
            style="margin-left: 0.5%;"
            v-show="inEditingMode"
            @click="inEditingMode = false;"
        />
        <a-button
            type="primary"
            shape="circle"
            icon="check"
            size="small"
            style="margin-left: 0.5%;"
            v-show="inEditingMode"
            @click="inEditingMode = false;form.have += addNumber;"
        />
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
        <a-select v-model="form.BelongTo" placeholder="请选择药品的所属人" :default-value="form.BelongTo">
          <a-select-option v-for="(l) in name_listo" :value="l" :key="l">
            {{l}}
          </a-select-option>
        </a-select>
      </a-form-model-item>
      <a-form-model-item label="药品用法" prop="usage">
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
        <a-button type="primary" @click="showModal">
          提交
        </a-button>
      </a-form-model-item>
    </a-form-model>
    <a-modal v-model="visible" title="请确认你所做的更改" ok-text="确认" cancel-text="取消" @ok="submit">
      <p v-html="whereChanged.str"></p>

    </a-modal>
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
      addNumber:0,
      inEditingMode:false,
      form: {
        name: this.$store.state.MedicineOptions.item.name,
        desc: this.$store.state.MedicineOptions.item.description,
        room: this.$store.state.MedicineOptions.item.room,
        stat: this.$store.state.MedicineOptions.item.stat,
        open: true,
        endby:this.$store.state.MedicineOptions.item.endby,
        endbyT:this.$store.state.MedicineOptions.item.endbyT,
        creator:this.$store.state.MedicineOptions.item.creatorID,
        BelongTo:this.$store.state.MedicineOptions.item.BelongTo,
        usage:{
          once: this.$store.state.MedicineOptions.item.usage.once,
          times: this.$store.state.MedicineOptions.item.usage.times,
        },
        have:0,
      },
      org_form:{
        name: this.$store.state.MedicineOptions.item.name,
        desc: this.$store.state.MedicineOptions.item.description,
        room: this.$store.state.MedicineOptions.item.room,
        stat: this.$store.state.MedicineOptions.item.stat,
        open: true,
        endby:this.$store.state.MedicineOptions.item.endby,
        endbyT:this.$store.state.MedicineOptions.item.endbyT,
        creator:this.$store.state.MedicineOptions.item.creatorID,
        BelongTo:this.$store.state.MedicineOptions.item.BelongTo,
        usage:{
          once: this.$store.state.MedicineOptions.item.usage.once,
          times: this.$store.state.MedicineOptions.item.usage.times,
        },
        have:0,
      },
      whereChanged:{
        str:"",
        cgs:[]
      },
      visible: false,
      rules: {
        name: [
          { required: true, message: '请输入药品名称', trigger: 'blur' },
          { min: 2, max: 12, message: '长度必须为2~12位', trigger: 'blur' },
        ],
      },
    };
  },
  methods: {
    showModal() {
      this.findWhereChanged();
      if (this.whereChanged.cgs.length === 0){
        this.$message.error("请至少修改一项内容!");
        return;
      }
      this.visible = true;
    },
    submit(){
      this.visible = false;
      const sendData = new FormData();
      sendData.append('token', this.$store.state.UserOptions.users.token);
      sendData.append('userid', this.$store.state.UserOptions.users.id);
      sendData.append('accessToken', this.$store.state.UserOptions.users.access);
      sendData.append('med_id', this.$store.state.MedicineOptions.item.id);
      sendData.append('edit',JSON.stringify(this.whereChanged.cgs));
      axios.post('https://home.caizhixiao.com.cn/Api/App/User/8yuzxcew97/edit.php',sendData).then(
          (res) => {
            if (res.data.code === 200){
              this.$message.success(res.data.text);
              this.$router.replace({name: 'SuccessPage'});
            }else{
              this.$message.error(res.data.text);
            }
          },
          (err) => {
            this.$message.error(err);
          }
      )
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

    },
    findWhereChanged() {
      let str = ``;
      let changes = [];
      let countChanged = false, usageChanged = false, statChanged = false;
      let duse = this.org_form.usage.once * this.org_form.usage.times,lst_duse = duse;
      if (this.form.name !== this.org_form.name) {
        str += `<strong class="title">药品名称:</strong>${this.org_form.name}
                ---->
                <strong>${this.form.name}</strong><br/>`;
        changes.push({
          "key": 'Name',
          "old": this.org_form.name,
          "new": this.form.name,
        });
      }
      if (this.form.have !== this.org_form.have) {
        countChanged = true;
        str += `<strong class="title">药品数量:</strong>${this.org_form.have}
                ---->
                <strong>${this.form.have}</strong><br/>`;
      }
      if (this.form.BelongTo !== this.org_form.BelongTo) {
        str += `<strong class="title">药品所属人:</strong>${this.org_form.BelongTo}
                ---->
                <strong>${this.form.BelongTo}</strong><br/>`;
        changes.push({
          "key": 'BelongTo',
          "old": this.org_form.BelongTo,
          "new": this.form.BelongTo,
        })
      }
      if (this.form.usage.times !== this.org_form.usage.times) {
        usageChanged = true;
        str += `<strong class="title">药品用法:</strong>一天${this.org_form.usage.times}次
                ---->
                <strong>一天${this.form.usage.times}次</strong><br/>`;
      }
      if (this.form.usage.once !== this.org_form.usage.once) {
        usageChanged = true;
        str += `<strong class="title">药品用法:</strong>一次${this.org_form.usage.once}个(单位)
                ---->
                <strong>一次${this.form.usage.once}个(单位)</strong><br/>`;
      }
      if (this.form.room !== this.org_form.room) {
        str += `<strong class="title">药品科室:</strong>${this.org_form.room}
                ---->
                <strong>${this.form.room}</strong><br/>`;
        changes.push({
          "key": 'Room',
          "old": this.org_form.room,
          "new": this.form.room,
        })
      }
      if (this.form.open !== this.org_form.open) {
        statChanged = true;
        str += `<strong class="title">药品状态:</strong>${this.org_form.open === true ? '开启' : '关闭'}
                ---->
                <strong>${this.form.open === true ? '开启' : '关闭'}</strong><br/>`;
      }
      if (this.form.desc !== this.org_form.desc) {
        str += `<strong class="title">药品描述:</strong>${this.org_form.desc}
                ---->
                <strong>${this.form.desc}</strong><br/>`;
        changes.push({
          "key": 'Description',
          "old": this.org_form.desc,
          "new": this.form.desc,
        })
      }
      if (usageChanged === true) {
        lst_duse = duse;
        duse = this.form.usage.once * this.form.usage.times;
        changes.push({
          key: 'DailyUse',
          old: JSON.stringify(this.org_form.usage),
          new: JSON.stringify(this.form.usage),
        });
        if (countChanged === false){
          if (this.form.stat >= 0){
            changes.push({
              'key': 'Stat',
              'old': this.org_form.stat,
              'new': Math.floor(this.form.stat*lst_duse/duse)
            })
          }else{
            let endbyt = new Date();
            endbyt.setDate(endbyt.getDate() + Math.floor(this.form.endby*lst_duse/duse));
            changes.push({
              'key': 'EndBy',
              'old': this.org_form.endbyT,
              'new': endbyt
            })
          }
        }
      }
      if (countChanged === true) {
        let can_use_add = Math.floor(Math.abs(this.form.have - this.org_form.have) / duse);
        if (this.form.have - this.org_form.have < 0){
          can_use_add = -can_use_add;
        }
        statChanged = false;
        //true/false --> false
        if (this.form.open === false) {
          if (this.org_form.open === false) {
            changes.push({
              'key': 'Stat',
              'old': this.org_form.stat,
              'new': Math.floor(this.form.stat*lst_duse/duse) + can_use_add
            });
          } else {
            // true ----> false
            changes.push({
              'key': 'Stat',
              'old': this.org_form.stat,
              'new': Math.floor(this.form.endby*lst_duse/duse) + can_use_add
            })
          }
        }
        //true/false --> true
        else {
          if (this.org_form.open === true) {
            let endbyt = new Date();
            //注意后端的endby = endbyt，
            //而前段的endby是剩余天数
            //所以需要前段将endbyt的值传给后端时，名字必须为endby
            endbyt.setDate(endbyt.getDate() + Math.floor(this.form.endby*lst_duse/duse) + can_use_add);
            changes.push({
              'key': 'Endby', //这里的endby是后端的endby即前端的endbyt
              'old': this.org_form.endbyT,
              'new': endbyt
            });
          } else {
            let endbyt = new Date();
            endbyt.setDate(endbyt.getDate() + can_use_add + Math.floor(this.form.stat*lst_duse/duse));
            //注意后端的endby = endbyt，
            //而前段的endby是剩余天数
            //所以需要前段将endbyt的值传给后端时，名字必须为endby
            changes.push({
              'key': 'Endby', //这里的endby是后端的endby即前端的endbyt
              'old': this.org_form.endbyT,
              'new': new Date(endbyt)
            });
            changes.push({
              'key': 'Stat', //这里的endby是后端的endby即前端的endbyt
              'old': this.org_form.stat,
              'new': -1
            });
          }
        }
      }
      if (statChanged === true){
        if (this.form.open === true){
          //false ---> true
          changes.push({
            key:'Stat',
            old:this.org_form.stat,
            new:-1,
          });
          let t = new Date();
          t.setDate(t.getDate() + Math.floor(this.org_form.stat));
          changes.push({
            key:'EndBy',
            old:this.org_form.endbyT,
            new:t
          });
        }else{
          //true ---> false
          changes.push({
            key:'Stat',
            old:-1,
            new:Math.floor(this.form.have / (this.form.usage.once * this.form.usage.times))
          })
        }
      }
      let cgs = {};
      for (let i = 0;i < changes.length ; i++){
        cgs[changes[i].key] = {new:changes[i].new,old:changes[i].old};
      }
      let result = {
        str,
        cgs,
      };
      this.whereChanged = result;
    }
  },
  computed: {
    name_listo() {
      return this.name_list.filter((t) => {
        return t.indexOf("testAccount") === -1;
      });
    },
  },
  mounted() {
    setTimeout(() => {
      this.getNameList();
      this.form.have = Math.floor(this.form.usage.times * this.form.usage.once * (this.form.stat >= 0 ? this.form.stat : this.form.endby));
      this.org_form.have = this.form.have;
      this.org_form.open = this.form.open = (this.org_form.stat === "-1");
      if (this.form.creator === undefined){
        this.$message.error("无法获取UID，请刷新重试!");
      }
    },100);
  }
}
</script>

<style scoped>
.title {
  font-weight: 600;
  padding-right: 1.5px;
}
</style>