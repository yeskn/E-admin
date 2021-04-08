import {reactive, toRaw} from "vue";
import request from '@/utils/axios'
import {findTree,setObjectValue} from '@/utils'
import {response} from "express";

export const store = Symbol()
// 使用 reactive 函数完成响应式转换
const states = reactive({
    device:'desktop',
    routerStatus: true,
    //侧边栏
    sidebar: {
        //打开关闭
        opend: true,
        //显示隐藏
        visible: true,
    },
    //主内容组件渲染
    mainLoading: false,
    mainComponent: [],
    mainTitle: '',
    mainDescription: '',
    component: null,
    componentVariable: [],
    proxyData: {},

    //错误信息
    errorPage: {
        visable: false,
        data: null,
    },
    //个人信息
    info: {
        id: 0,
        webLogo: '',
        webName: '',
        dropdownMenu: [],
    },
    //菜单
    menus: [],
    menuModule: '',
    breadcrumb: [],
});
export const state = states
//操作方法
const action = {
    //设置面包屑
    setBreadcrumb(data: any) {
        states.breadcrumb = data
    },
    //侧边栏打开关闭
    sidebarOpen: function (bool: boolean) {
        states.sidebar.opend = bool
    },
    //显示隐藏侧边栏
    sidebarVisible: function (bool: boolean) {
        states.sidebar.visible = bool
    },
    //设置绑定值
    setProxyData(path,value){
        setObjectValue(states.proxyData,path,value)
    },
    //设置加载状态
    loading: function (bool: boolean) {
        states.mainLoading = bool
    },
    //缓存组件变量
    cachesVariable(url: string) {
        const index = action.getComponentIndex(url)

        if (index > -1) {
            // @ts-ignore
            states.componentVariable[index].proxyData = {...states.proxyData}

        }
    },
    device(device:string){
        states.device = device
    },
    clearComponent(url: string) {
        const index = action.getComponentIndex(url)
        states.mainComponent.splice(index, 1)
        states.componentVariable.splice(index, 1)
    },
    getComponentIndex(url: string) {
        return states.mainComponent.findIndex(item => {
            // @ts-ignore
            return item.url === url
        })
    },
    //设置主内容组件
    component: function (data: object, url: string) {
        state.component = null
        const index = action.getComponentIndex(url)
        for (let i in states.proxyData) {
            // @ts-ignore
            delete states.proxyData[i]
        }
        if (index === -1) {
            // @ts-ignore
            const menu = findTree(state.menus, url.substr(1), 'url')
            if (menu) {

                states.componentVariable.push({
                    // @ts-ignore
                    url: url,
                    // @ts-ignore
                    proxyData: {}
                })

                states.mainComponent.push({
                    // @ts-ignore
                    title: menu.name || url,
                    // @ts-ignore
                    description: data.bind.eadmin_description || '',
                    // @ts-ignore
                    url: url,
                    // @ts-ignore
                    component: data,
                })
            } else {
                // @ts-ignore
                state.component = data
            }

            // @ts-ignore
            state.mainTitle = data.bind.eadmin_title || ''
            // @ts-ignore
            state.mainDescription = data.bind.eadmin_description || ''
        } else {
            // @ts-ignore
            for (let field in states.componentVariable[index].proxyData) {
                // @ts-ignore
                states.proxyData[field] = states.componentVariable[index].proxyData[field]
            }
            // @ts-ignore
            state.mainTitle = states.mainComponent[index].title || ''
            //@ts-ignore
            state.mainDescription = states.mainComponent[index].description || ''
        }
        action.loading(false)
    },
    //关闭错误页面
    errorPageClose() {
        states.errorPage.visable = false
    },
    //打开错误页面
    errorPageOpen(data: object) {
        // @ts-ignore
        states.errorPage.data = data
        states.errorPage.visable = true
    },
    //选择头部菜单模块
    selectMenuModule(id: string) {
        states.menuModule = id
    },
    refreshToken() {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/admin/refreshToken'
            }).then((res: any) => {
                if (res.data.token) {
                    localStorage.setItem('eadmin_token', res.data.token)
                    resolve(res)
                } else {
                    localStorage.removeItem('eadmin_token')
                    reject(res)
                }
            }).catch((res: any) => {
                reject(res)
            })
        })
    },
    getInfo() {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/admin/info'
            }).then((res: any) => {
                const info = res.data.info
                states.menus = res.data.menus
                if (info) {
                    states.component = null
                    states.info = info
                    states.info.webLogo = res.data.webLogo
                    states.info.webName = res.data.webName
                    states.info.dropdownMenu = res.data.dropdownMenu
                }
                resolve(res)
            }).catch((res: any) => {
                reject(res)
            })
        })
    },
    login(data: object) {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/login',
                method: 'post',
                data: data
            }).then((res: any) => {
                states.mainComponent = []
                states.componentVariable = []
                resolve(res)
            }).catch((res: any) => {
                reject(res)
            })
        })
    },
    logout() {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/login/logout'
            }).then((res: any) => {
                states.info.id = 0
                states.mainComponent = []
                states.componentVariable = []
                localStorage.removeItem('eadmin_token')
                resolve(res)
            }).catch((res: any) => {
                reject(res)
            })
        })
    }
}
export {action}

