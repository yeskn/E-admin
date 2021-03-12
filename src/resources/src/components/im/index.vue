<template>
    <!-- 头像消息框 -->
    <div class="miniBox" @click="openIm">
        <el-avatar :src="info.headimg" style="margin-right: 10px"></el-avatar>
        <el-badge :value="unReadNum" :max="99" v-if="unReadNum > 0">{{info.nickname}}</el-badge>
        <el-badge :type="online" is-dot v-else>{{info.nickname}}</el-badge>
    </div>
    <el-dialog
            v-model="dialogVisible"
            width="900px"
            :close-on-click-modal="false"
            :show-close="false"
            :fullscreen="fullscreen"
    >
        <div class="main">
            <left-tools v-model="selectTools" :headimg="info.headimg" :unReadFriendNum="unReadFriendNum" :unReadNum="unReadNum" :customerConnNum="customerConnNum"></left-tools>
            <list :list="recentList" :select-tool="selectTools"></list>
            <div style="flex: 1">
                <div style="position: absolute;right: 5px;top:5px;cursor: pointer;">
                    <i class="el-icon-full-screen rightTools"></i>
                    <i class="el-icon-close rightTools" @click="dialogVisible=false"></i>
                </div>
                <im-main :select-tool="selectTools"></im-main>
            </div>
        </div>
    </el-dialog>
</template>

<script>
    import {defineComponent,reactive,toRefs} from "vue";
    import leftTools from './leftTools.vue'
    import list from './list/list.vue'
    import ImMain from './main/main.vue'
    import im from './websocket/websocket'
    export default defineComponent({
        name: "imIndex",
        components:{
            leftTools,
            list,
            ImMain
        },
        setup(){
            const state = reactive({
                dialogVisible:false,
                fullscreen:false,
                info:{
                    id:0,
                    headimg:'',
                    nickname:'',
                },
                online: 'danger',
                //最近会话
                recentList:[],
                //好友列表
                friendList:[],
                unReadNum:0,
                customerConnNum:0,
                unReadFriendNum:0,
                selectTools:'message',
            })
            im.connect()
            im.onMessage((action,data)=>{
                switch (action) {
                    //登录成功
                    case 'login':
                        if(data.code === 0){
                            state.recentList = []
                            state.friendList = []
                            state.online = 'success'
                            state.info = data.info
                            state.unReadFriendNum = data.init.unReadFriendNum
                            state.customerConnNum = data.init.customerConnNum
                            //获取会话列表
                            im.send('recentList')
                            //获取好友列表
                            im.send('friendList')
                        }
                        break;
                    //获取添加好友列表
                    case 'getAddFriend':
                        state.unReadFriendNum = 0
                        break;
                    //收到好友请求
                    case 'addFriend':
                        if(state.selectTools == 'friend'){
                            im.send('getAddFriend')
                        }
                        state.unReadFriendNum = data.num
                        break
                }
            })

            function openIm(){
                this.dialogVisible=true
                // if(this.nowMsgUid > -1){
                //     let index = this.getMsgIdKey(this.recentList,this.nowMsgUid, 'from_uid')
                //     this.selectMsgUser(this.recentList[index], index)
                // }
            }
            return {
                ...toRefs(state),
                openIm
            }
        }
    })
</script>

<style scoped>
    .main{

        top: 0;
        left: 0;
        position: absolute;
        min-width: 900px;
        height: 700px;
        display: flex;
    }
    .miniBox {
        padding: 0 20px;
        height: 60px;
        background: #ffffff;
        position: fixed;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 1px 1px 50px #cccccc;
        border-radius: 5px;
        cursor: pointer;
        z-index: 100;
    }
    .rightTools {
        margin: 0 5px;
    }
</style>
