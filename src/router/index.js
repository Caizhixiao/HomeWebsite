import VueRouter from "vue-router";
import UserLogin from "@/pages/user/UserLogin.vue";
//import MedicineList from "@/pages/medicine/MedicineList.vue";
import HomePage from "@/pages/HomePage.vue";
import $store from '../store/index.js';
import MyApp from "@/pages/MyApp/MyApp.vue";
import MedicineList from "@/pages/MyApp/medicine/MedicineList.vue";
import NoAccess from "@/pages/user/NoAccess.vue";
import MedicineAdd from "@/pages/MyApp/medicine/MedicineAdd.vue";
import MedicineDetails from "@/pages/MyApp/medicine/MedicineDetails.vue";
import SuccessPage from "@/pages/MyApp/medicine/SuccessPage.vue";
import EmailAccept from "@/pages/EmailAccept.vue";
import EmailRefuse from "@/pages/EmailRefuse.vue";
import BlockLogin from "@/pages/BlockLogin.vue";
import BlockIP from "@/pages/BlockIP.vue";
import UserLogout from "@/pages/user/UserLogout.vue";
import MedicineEdit from "@/pages/MyApp/medicine/MedicineEdit.vue";
import AskDecision from "@/pages/AskDecision.vue";
const originalPush = VueRouter.prototype.push ;
VueRouter.prototype.push = function push(location) {
    return originalPush.call(this, location).catch(err => err)
}
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home_user_login',
            component: UserLogin,
            meta: {
                requireAuth: 0
            }
        },
        {
            path: '/login',
            name: 'user_login',
            component: UserLogin,
            meta: {
                requireAuth: 0
            }
        },
        {
            path:'/logout',
            name: 'user_logout',
            component: UserLogout,
            meta: {
                requireAuth: 1
            }
        },
        {
            path:'/email_accepted/:id',
            name:'verAcc',
            component: EmailAccept,
        },
        {
            path:'/email_refused/:id',
            name:'refAcc',
            component: EmailRefuse,
        },
        {
            path:'/blocked',
            name:'block',
            component: BlockLogin
        },
        {
            path:'/blockIP',
            name:'blockIP',
            component: BlockIP
        },
        {
            path:'/AskDecision/:id',
            name:'AskDecision',
            component: AskDecision,
        },
        {
            path:'/home',
            name:'home',
            component:HomePage,
            meta: {
                requireAuth: 1
            }
        },
        {
            path:'/noAccess',
            name:'noAccess',
            component:NoAccess,
        },
        {
            path: '/app',
            name: 'MyApp',
            component: MyApp,
            children:[
                {
                    path:'8yuzxcew97',
                    name:'MedicineList',
                    component:MedicineList,
                    children:[
                        {
                            path: 'add',
                            name: 'MedicineAdd',
                            component: MedicineAdd,
                            meta: {
                                requireAuth: 1
                            }
                        },
                        {
                            path: 'detail',
                            name: 'MedicineDetails',
                            component: MedicineDetails,
                            meta: {
                                requireAuth: 1
                            }
                        },
                        {
                            path: 'edit',
                            name: 'MedicineEdit',
                            component: MedicineEdit,
                            meta: {
                                requireAuth: 1
                            }
                        },
                        {
                            path: 'success',
                            name: 'SuccessPage',
                            component: SuccessPage,
                            meta: {
                                requireAuth: 1
                            }
                        }
                    ],
                    meta: {
                        requireAuth: 1
                    }
                }
            ],
            meta: {
                requireAuth: 1
            }
        }
    ]
});
let cookie = {
    getCookie(cname) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for(let i=0; i<ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(name) === 0) return c.substring(name.length,c.length);
        }
        return "";
    },
}
router.beforeEach((to,from,next)=>{
    if(to.meta.requireAuth){ //判断当前路由是否需要进行权限控制
        if (cookie.getCookie('userid') === ''){
            next('noAccess');
            return;
        }
        let acc = $store.state.UserOptions.users.accessN;
        if (acc === ''){
            let user_data = {
                token:cookie.getCookie('token'),
                userid:cookie.getCookie('userid'),
                accessToken:cookie.getCookie('accessToken'),
                username:cookie.getCookie('username'),
            }
            $store.commit('UserOptions/LOGIN',user_data);
            $store.dispatch('UserOptions/UserAccess',
                {
                    userid:cookie.getCookie('userid'),
                    accessToken:cookie.getCookie('accessToken')
                }
            ).then(() => {
                setTimeout(() => {
                    acc = $store.state.UserOptions.users.accessN;
                    console.log(acc);
                    if(acc >= to.meta.requireAuth){ //权限控制的具体规则
                        next() //放行
                    }else{
                        next('noAccess') //跳转到无权限页面
                    }
                },1000);
            });
        }
        else{
            if(acc >= to.meta.requireAuth){ //权限控制的具体规则
                next() //放行
            }else{
                next('noAccess') //跳转到无权限页面
            }
        }
    }else{
        next() //放行
    }
});
export default router;