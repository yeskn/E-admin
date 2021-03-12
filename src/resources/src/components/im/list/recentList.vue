<template>
    <div class="item" v-for="(item,index) in list">
        <div>
            <el-avatar style="margin-left: 10px;" shape="square"
                       :src="item.msg_type === 'customerMsg' ? item.user_headimg:item.headimg"></el-avatar>
        </div>
        <div style="flex:1;margin-left: 10px;">
            <div class="name">{{item.msg_type === 'customerMsg' ? item.user_nickname:item.nickname}}</div>
            <div class="content">{{item.content}}</div>
        </div>
        <div style="margin-right: 10px;text-align: right;width: 70px">
            <div class="time">{{item.time}}</div>
            <div class="notice">
                <el-badge :value="item.unReadNum"
                          v-show="item.unReadNum > 0"></el-badge>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs} from "vue";
    export default defineComponent({
        name: "recentList",
        props:{
          list:Array
        },
        setup(){

        }
    })
</script>

<style scoped>
    .item {
        height: 60px;
        display: flex;
        align-items: center;
        border-bottom: solid 1px #EEEEEE;
    }
</style>
