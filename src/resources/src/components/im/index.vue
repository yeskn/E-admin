<template>
    <!-- 头像消息框 -->
    <div class="miniBox" @click="openIm">
        <el-avatar :src="info.headimg" style="margin-right: 10px"></el-avatar>
        <el-badge :value="unReadNum" type="danger" :max="99" v-if="unReadNum > 0">{{info.nickname}}</el-badge>
        <el-badge :type="online" is-dot v-else>{{info.nickname}}</el-badge>
    </div>
    <div v-show="dialogShow" @click.native="popoverVisibleClose">
        <el-dialog
                v-model="dialogVisible"
                width="900px"
                :close-on-click-modal="false"
                :show-close="false"
                :fullscreen="fullscreen"
        >
            <div class="main" @click="popoverVisibleClose">
                <left-tools :headimg="info.headimg"></left-tools>
                <list></list>
                <div class="mainContent">
                    <div style="position: absolute;right: 5px;top:5px;cursor: pointer;">
                        <el-tooltip effect="light" content="转接" v-show="recentType == 'customerMsg'">
                            <i @click="transferVisible = true" class="el-icon-refresh rightTools"></i>
                        </el-tooltip>
                        <el-tooltip effect="light" content="结束会话" v-show="recentType == 'customerMsg'">
                            <i @click="closeCustomer()" class="el-icon-switch-button rightTools"></i>
                        </el-tooltip>
                        <i class="el-icon-full-screen rightTools"></i>
                        <i class="el-icon-close rightTools" @click="dialogVisible=false"></i>
                    </div>
                    <im-message v-show="leftTool === 'message'"></im-message>
                    <im-friend v-show="leftTool === 'friend'"></im-friend>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {defineComponent,reactive,toRefs,nextTick,watch} from "vue";
    import leftTools from './leftTools.vue'
    import list from './list/list.vue'
    import ImMessage from './main/message.vue'
    import ImFriend from './main/friend.vue'
    import im from './websocket/websocket'
    export default defineComponent({
        name: "EadminIm",
        components:{
            leftTools,
            list,
            ImMessage,
            ImFriend
        },
        props:{
            username:String,
            password:String,
            websocket:String,
        },
        setup(props){
            const state = reactive({
                dialogShow:false,
                dialogVisible:true,
                fullscreen:false,
                info:{
                    id:0,
                    headimg:'',
                    nickname:'',
                },
                online: 'danger',
            })


            nextTick(()=>{
                im.connect(props.websocket,props.username,props.password)
                state.dialogVisible = false
            })
            im.onMessage((action,data)=>{
                switch (action) {
                    //登录成功
                    case 'login':
                        if(data.code === 0){
                            state.online = 'success'
                            state.info = data.info
                        }
                        break;
                }
            })
            im.onClose(e=>{
                state.online = 'danger'
            })
            watch(()=>im.state.leftTool,val=>{
                if(val === 'message'){

                }else if(val === 'friend'){
                    im.send('getAddFriend')
                }
            })
            // //未读数量
            // const unReadNum = computed(()=>{
            //     let unReadNum = 0
            //     im.state.recentList.forEach(item => {
            //         unReadNum += parseInt(item.unReadNum)
            //     })
            //
            //     return unReadNum
            // })
            function openIm(){
                state.dialogShow = true
                state.dialogVisible = true
                // if(this.nowMsgUid > -1){
                //     let index = this.getMsgIdKey(this.recentList,this.nowMsgUid, 'from_uid')
                //     this.selectMsgUser(this.recentList[index], index)
                // }
            }
            function popoverVisibleClose() {
                im.state.msgList.forEach(item => {
                    item.popoverVisible = false
                })
            }
            //结束客服会话
            function closeCustomer(groupId){
                // this.send('会话已结束',1)
                // const index = this.getMsgIdKey(this.recentList, groupId, 'group_id')
                // const recent_id = im.state.recentList[index].recent_id || ''
                // im.state.recentList.splice(index,1)
                // im.state.msgList = []
                // setTimeout(()=>{
                //     im.send('removeRecent',{
                //         recent_id:recent_id,
                //     })
                // },1000)
            }
            return {
                closeCustomer,
                ...toRefs(state),
                ...toRefs(im.state),
                openIm,
                popoverVisibleClose
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
    .mainContent{
        height: 100%;
        background: #ffffff;
        flex: 1;
    }

</style>
