<template>
    <div>
        <div class="item" :style="{backgroundColor: im.isSelectUser(item)?'#c5c4c4':''}" v-for="(item,index) in list" @click="im.selectUser(item,index)">
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
                    <el-badge type="danger" :value="item.unReadNum" v-show="item.unReadNum > 0"></el-badge>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import im from '../websocket/websocket'
    import {defineComponent, toRefs ,computed} from "vue";
    export default defineComponent({
        name: "ImRecentList",
        props:{
            search:String,
        },
        setup(props){
            im.onMessage((action,data)=>{
                switch (action) {
                    //登录成功
                    case 'login':
                        if(data.code === 0){
                            im.state.recentList = []
                            //获取会话列表
                            im.send('recentList')
                        }
                        break;
                    //收到最近会话列表
                    case 'recentList':
                        im.state.recentList = im.state.recentList.concat(data)
                        break;
                }
            })
            const list = computed(()=>{
                im.state.recentList = im.state.recentList.sort(function (a, b) {
                    if (a.unReadNum > b.unReadNum) {
                        return -1
                    }else{
                        return 0
                    }
                })
                return im.state.recentList.filter(item => {
                    return item.nickname.indexOf(props.search) >= 0
                })
            })
            return {
                list,
                im,
                ...toRefs(im.state)
            }
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
    .item:hover {
        background: #dedcda;
        cursor: pointer;
    }
</style>
