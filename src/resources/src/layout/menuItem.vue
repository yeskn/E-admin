<template>
    <el-submenu v-if="menu.children" index="2">
        <template #title> <i :class="menu.icon" v-if="menu.icon"></i><span slot="title">{{menu.name}}</span></template>
        <template v-for="item in menu.children">
            <menu-item v-if="item.children" :menu="item"></menu-item>
            <el-menu-item :index="item.id" v-else>
                <i :class="item.icon" v-if="item.icon"></i>
                <span slot="title">{{item.name}}</span>
            </el-menu-item>
        </template>
    </el-submenu>
    <el-menu-item :index="menu.id" v-else>
        <i :class="menu.icon" v-if="menu.icon"></i>
        <span slot="title">{{menu.name}}</span>
    </el-menu-item>
</template>
<script>
    import {defineComponent} from 'vue'
    export default defineComponent({
        name: "menuItem",
        props:{
            menu:[Object,Array]
        },
        setup(props){
            const menu = props.menu
            return {
                menu
            }
        }
    })
</script>

<style scoped>

</style>
