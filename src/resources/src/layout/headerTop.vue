<template>
    <div class="header-container">
        <i v-if="sidebar.visible" :class="[sidebar.opend?'el-icon-s-unfold hamburger':'el-icon-s-fold hamburger']" style="font-size: 18px" @click="collapse"/>
        <el-menu :default-active="activeIndex" @select="selectMenu" class="el-menu-demo" mode="horizontal" >
            <el-menu-item v-for="item in menus" :index="item.id+''">
                <i :class="item.icon" v-if="item.icon"></i>
                <span slot="title">{{item.name}}</span>
            </el-menu-item>
        </el-menu>
    </div>
    <div class="header-title" v-if="state.proxyData.eadmin_title">
        <div class="title" >{{state.proxyData.eadmin_title}}</div>
        <div class="breadcrumb">
            <breadcrumb></breadcrumb>
            <i class="el-icon-back back" @click="back"></i>
        </div>
    </div>
</template>

<script>
    import breadcrumb from '/@/components/breadcrumb.vue'
    import {useRoute,useRouter} from 'vue-router'
    import { link,findParent,findTree } from '/@/utils'
    import {defineComponent, watch, inject, computed} from 'vue'
    import { store, action} from '/@/store'
    export default defineComponent({
        name: "headerTop",
        components:{
            breadcrumb,
        },
        setup() {
            const route = useRoute()
            const router = useRouter()
            const state = inject(store)
            const sidebar = state.sidebar
            const menus = state.menus
            const activeIndex = computed(()=>{
                let menu = findTree(state.menus,route.path,'url'),menuLevels = []
                if(menu){
                    menuLevels = findParent(state.menus,menu.pid)
                    let menuId
                    if(menuLevels.length > 0){
                        menuId = menuLevels[0].id
                        action.selectMenuModule(menuId)
                    }
                    menuLevels.push(menu)
                    if(menu.pid === 0){
                        action.selectMenuModule(menu.id)
                    }
                    action.setBreadcrumb(menuLevels)
                    return menuLevels[0].id+''
                }else{
                    return state.menuModule+''
                }
            })
            watch(()=>state.menuModule,(val,oldVal)=>{
                menus.forEach(item=>{
                    if(item.id == val && item.children){
                        action.sidebarVisible(true)
                        let url = defaultMenu(item.children)
                        if(url){
                            link(url)
                        }
                    }else{
                        action.sidebarVisible(false)
                    }
                })
            })
            //侧边栏展开收缩
            function collapse(){
                action.sidebarOpen(!sidebar.opend)
            }
            //选择菜单
            function selectMenu(index,indexPath) {
                let menu = findTree(menus,index,'id')
                action.selectMenuModule(index)
                if(!menu.children){
                    link(menu.url)
                }
            }
            //查找当前第一个菜单
            function defaultMenu(menus) {
                for(let key in menus){
                    if(menus[key].children){
                        let item = defaultMenu(menus[key].children)
                        if(item){
                            return item
                        }
                    }else{
                        return menus[key].url
                    }
                }
                return null
            }
            function back() {
                router.back()
            }
            return {
                activeIndex,
                state,
                selectMenu,
                collapse,
                sidebar,
                menus,
                back
            }
        }
    })
</script>

<style scoped>
    .header-container {
        display: flex;
        align-items: center;
        background: #FFFFFF;
        height: 60px;
        width: 100%;
        border-bottom:1px solid #f6f6f6;
    }
    .header-title .title{
        font-size: 16px;
    }
    .header-title{
        display: flex;
        justify-content: space-between;
        height: 35px;
        padding-left:15px;
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #ededed;
        box-shadow: 0 1px 4px rgba(0,21,41,.08);
    }
    .hamburger {
        padding: 0 10px;
        cursor: pointer;
    }
    .breadcrumb{
        display: flex;
        align-items: center;
    }
    .back{
        margin-left: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-left: 1px solid #ededed;
        height: 35px;
        width: 40px;
        cursor: pointer
    }
    .back:hover{
        background: #EFEFEF;
    }
</style>
