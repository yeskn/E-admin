import { reactive } from "vue";
import request from '/@/utils/axios'
export const store = Symbol()
// 使用 reactive 函数完成响应式转换
const states = reactive({
    //侧边栏
    sidebar: {
        //打开关闭
        opend:true,
        //显示隐藏
        visible:true,
    },
    //主内容组件渲染
    mainComponent:null,
    proxyData:{},
    //错误信息
    errorPage:{
        visable:false,
        data:null,
    },
    //个人信息
    info:{
        id:0,
    },
    //菜单
    menus:[],
    menuModule:0
});
export const state = states
//操作方法
const action = {
    //侧边栏打开关闭
    sidebarOpen:function(bool) {
        states.sidebar.opend = bool
    },
    //显示隐藏侧边栏
    sidebarVisible:function(bool) {
        states.sidebar.visible = bool
    },
    //设置主内容组件
    component:function(data){
        states.mainComponent = data
    },
    //关闭错误页面
    errorPageClose(){
        states.errorPage.visable = false
    },
    //打开错误页面
    errorPageOpen(data){
        states.errorPage.data = data
        states.errorPage.visable = true
    },
    //选择头部菜单模块
    selectMenuModule(id){
       states.menuModule = id
    },
    getInfo(){
        return new Promise((resolve, reject) =>{
            request({
                url:'/admin/admin/info'
            }).then(res=>{
                const info = res.data.info
                states.menus = res.data.menus
                if(info){
                    states.info.id = info.id
                }
                resolve(res)
            }).catch(res=>{
                reject(res)
            })
        })
    }
}
export {action}

