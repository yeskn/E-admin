import { reactive } from "vue";
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
    }
});
export const state = states
//操作方法
const action = {
    //侧边栏打开关闭
    sidebarOpen:function(bool) {
        states.sidebar.opend = bool
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
    }
}
export {action}

