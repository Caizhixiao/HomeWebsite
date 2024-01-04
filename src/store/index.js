import Vue from 'vue';
import Vuex from 'vuex';
import axios from "axios";
const UserOptions = {
    namespaced: true,
    actions: {
        login(context,obj){
            const formData = new FormData();
            formData.append('userid', obj.userid);
            formData.append('token', obj.token);
            formData.append('accessToken', obj.accessToken);
            axios.post('https://home.caizhixiao.com.cn/Api/Account/login.php',formData)
                .then((response)=> {
                    let data = response.data;
                    if (data.code === 200){
                        this.$store.commit('UserOptions/LOGIN',obj);
                        Vue.prototype.$bus.$emit('getLoginMsg','SUCCESS');

                    }else{
                        Vue.prototype.$bus.$emit('getLoginMsg',data.text);
                    }
                })
                .catch((err)=>{
                    Vue.prototype.$bus.$emit('getLoginMsg',err);
                })
            context.commit('LOGIN',obj);
        },
        UserAccess(context,value){
            const formData = new FormData();
            formData.append('userid', value.userid);
            formData.append('accessToken', value.accessToken);

            axios.post('https://home.caizhixiao.com.cn/Api/Account/getAccess.php',formData)
                .then((response)=> {
                    let data = response.data;
                    if (data.code === 200){
                        switch(data.access){
                            case 'user':
                                context.commit('SET_ACCESS',1);
                                break;
                            case 'admin':
                                context.commit('SET_ACCESS',2);
                                break;
                            case 'root':
                                context.commit('SET_ACCESS',3);
                                break;
                            default:
                                context.commit('SET_ACCESS',0);
                                break;
                        }
                    }
                    else if (data.code === 0){
                        context.commit('SET_ACCESS',-1);
                    }
                    else{
                        context.commit('SET_ACCESS',0);
                    }
                })
                .catch(()=>{
                    context.commit('SET_ACCESS',0);
                })
        },
    },
    mutations: {
        LOGIN(state,obj){
            state.users.id = obj.userid;
            state.users.token = obj.token;
            state.users.access = obj.accessToken;
            state.users.username = obj.username;
        },
        SET_ACCESS(state,value){
            state.users.accessN = value;
        }
    },
    state:{
        users:{
            id:'',
            token:'',
            access:'',
            username:'未登录',
            accessN:'',
        }
    },
    getters:{

    }
};
const MedicineOptions = {
    namespaced: true,
    actions:{
        commit(context,value){
            const formData = new FormData();
            formData.append('userid', value.userid);
            formData.append('token', value.token);
            formData.append('accessToken', value.accessToken);
            formData.append('findUserID',value.creator+","+value.BelongTo);
            axios.post('http://home.caizhixiao.com.cn/Api/Account/findUserID.php',formData).then(
                response =>{
                    if (response.data.code === 200){
                        value.creator = response.data.result[0];
                        value.BelongTo = response.data.result[1];
                        value.usage = JSON.parse(value.usage);
                        value.LastUpdateTime = new Date(value.LastUpdateTime);
                        value.endbyT = new Date(value.endby);
                        let lft = new Date(value.endby) - new Date();
                        value.endby = (lft > 0 ? lft : 0);
                        context.commit('COMMIT',value);
                    }
                },
                (err)=>{
                    Vue.prototype.$bus.$emit('MedQuestErrMsg',err);
                }
            )
        }
    },
    mutations: {
        COMMIT(state,value){
            state.item = value;
        }
    },
    state:{
        item: {},
    },
    getters:{

    }
}
Vue.use(Vuex);
export default new Vuex.Store({
    modules: {
        UserOptions,
        MedicineOptions,
    }
});