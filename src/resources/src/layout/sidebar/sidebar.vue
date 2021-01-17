<template>
    <div :class="[sidebar.opend ? '':'collapse','sidebar-container']">
        <logo :collapse="sidebar.opend" ></logo>
        <el-menu :default-active="activeIndex"
                 text-color="#FFFFFF"
                 :collapse="!sidebar.opend"
                 :collapse-transition="true"
                 mode="vertical"
                 background-color="#000000"
                 @select="link"
                 >
            <menu-item v-for="item in menus" :menu="item"></menu-item>
        </el-menu>
    </div>
</template>

<script>
    import { link } from '/@/utils/validate'
    import Logo from '../logo.vue'
    import menuItem from './menuItem.vue'
    import { defineComponent,inject, ref ,computed } from 'vue'
    import { store } from '/@/store'
    export default defineComponent ({
        name: "sidebar",
        components: {
            Logo,
            menuItem,
        },
        setup() {
            const state = inject(store)
            const sidebar = state.sidebar
            const menus = computed(()=>{
                let menus = []
                state.menus.forEach(res=>{
                    if(res.id == state.menuModule && res.children){
                        menus = res.children
                    }
                })
                return menus
            })
            return {
                link,
                menus,
                sidebar,
                activeIndex: ref('')
            }
        }
    })
</script>

<style scoped>
.collapse{
    width: 64px !important;
}
</style>
