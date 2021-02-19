import { reactive} from "vue";
import request from '/@/utils/axios'
export const store = Symbol()
// 使用 reactive 函数完成响应式转换
const states = reactive({
    routerStatus:true,
    //侧边栏
    sidebar: {
        //打开关闭
        opend:true,
        //显示隐藏
        visible:true,
    },
    //主内容组件渲染
    mainLoading:false,
    mainComponent:[],
    componentVariable:[],
    proxyData:{},

    //错误信息
    errorPage:{
        visable:false,
        data:null,
    },
    //个人信息
    info:{
        id:0,
        webLogo:'',
        webName:'',
        dropdownMenu:[],
    },
    //菜单
    menus:[],
    menuModule:'',
    breadcrumb:[],
});
export const state = states
//操作方法
const action = {
    setProxyData(data){
        states.proxyData = data
    },
    //设置面包屑
    setBreadcrumb(data){
        states.breadcrumb = data
    },
    //侧边栏打开关闭
    sidebarOpen:function(bool) {
        states.sidebar.opend = bool
    },
    //显示隐藏侧边栏
    sidebarVisible:function(bool) {
        states.sidebar.visible = bool
    },
    //设置加载状态
    loading:function(bool){
        states.mainLoading = bool
    },
    //缓存组件变量
    cachesVariable(url) {
        const index = action.getComponentIndex(url)
        if(index > -1){
            states.componentVariable[index].proxyData = {...states.proxyData}

        }
    },

    clearComponent(url){
        const index = action.getComponentIndex(url)
        states.mainComponent.splice(index,1)
        states.componentVariable.splice(index,1)
    },
    getComponentIndex(url){
        return states.mainComponent.findIndex(item=>{
            return item.url === url
        })
    },
    //设置主内容组件
    component:function(data,url){
        const index = action.getComponentIndex(url)
        for(let i in states.proxyData){
            delete states.proxyData[i]
        }
        if(index > -1) {
            for(let field in states.componentVariable[index].proxyData){
                states.proxyData[field] = states.componentVariable[index].proxyData[field]
            }
        }else{
            states.componentVariable.push({
                url:url,
                proxyData:{}
            })
            states.mainComponent.push({
                title: data.bind.eadmin_title || url,
                url:url,
                component:data,
            })
        }
        action.loading(false)
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
    refreshToken(){
        return new Promise((resolve, reject) =>{
            request({
                url:'/admin/admin/refreshToken'
            }).then(res=>{
                if(res.data.token){
                    localStorage.setItem('eadmin_token',res.data.token)
                    resolve(res)
                }else{
                    localStorage.removeItem('eadmin_token')
                    reject(res)
                }
            }).catch(res=>{
                reject(res)
            })
        })
    },
    getInfo(){
        return new Promise((resolve, reject) =>{
            request({
                url:'/admin/admin/info'
            }).then(res=>{
                const info = res.data.info
                states.menus = res.data.menus
                if(info){
                    states.info = info
                    states.info.webLogo = res.data.webLogo
                    states.info.webName = res.data.webName
                    states.info.dropdownMenu = res.data.dropdownMenu
                }
                resolve(res)
            }).catch(res=>{
                reject(res)
            })
        })
    },
    logout(){
        return new Promise((resolve, reject) =>{
            request({
                url:'/admin/login/logout'
            }).then(res=>{
                states.info.id = 0
                localStorage.removeItem('eadmin_token')
                resolve(res)
            }).catch(res=>{
                reject(res)
            })
        })
    }
}
export {action}

