<template>
    <div :class="[sidebar.opend ? '':'collapse','sidebar-container']">
        <logo :collapse="sidebar.opend" ></logo>
        <el-menu :default-active="activeIndex"
                 text-color="#FFFFFF"
                 :collapse="!sidebar.opend"
                 :collapse-transition="true"
                 mode="vertical"
                 background-color="#000000"
                 @select="select"
                 >
            <menu-item v-for="item in menus" :menu="item"></menu-item>
        </el-menu>
    </div>
</template>

<script>
    import {useRoute} from 'vue-router'
    import { link,findParent,findTree } from '/@/utils'
    import Logo from '../logo.vue'
    import menuItem from './menuItem.vue'
    import { defineComponent,inject, computed} from 'vue'
    import { store ,action} from '/@/store'
    export default defineComponent ({
        name: "sidebar",
        components: {
            Logo,
            menuItem,
        },
        setup() {
            const route = useRoute()
            const state = inject(store)
            const sidebar = state.sidebar
            //当前激活菜单
            const activeIndex = computed(()=>{
                let menu = findTree(state.menus,route.path,'url')
                if(menu){
                    let menuLevels = findParent(state.menus,menu.pid)
                    menuLevels.push(menu)
                    action.setBreadcrumb(menuLevels)
                    action.selectMenuModule(menuLevels[0].id)
                    return menu.id+''
                }else{
                    return ''
                }
            })
            //侧边栏菜单渲染
            const menus = computed(()=>{
                let menus = []
                state.menus.forEach(res=>{
                    if(res.id == state.menuModule && res.children){
                        menus = res.children
                        let url = defaultMenu(menus)
                        if(url){
                            link(url)
                        }
                    }
                })
                return menus
            })
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
            //选择菜单
            function select(id,index) {
                let menu = findTree(state.menus,id,'id')
                link(menu.url)
            }
            return {
                select,
                menus,
                sidebar,
                activeIndex
            }
        }
    })
</script>

<style scoped>
.collapse{
    width: 64px !important;
}
</style>
