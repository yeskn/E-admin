<template>
    <div class="mainContent">
        <div class="header">{{nowMsgNickname}}</div>
        <!-- 内容区 -->
        <div class="msgContentBox">
            <div style="position: absolute;top: 0;left:45%" v-loading="scrollMsgLoading"></div>
            <el-scrollbar style="height:100%;" ref="chatMsgBox">
                <div v-for="(item,key) in msgList" class="msgItem">
                    <div v-if="!isDelMsg(item.msg_id)">
                        <div class="msgTime" v-if="key == 0">
                            <el-tag type="info" size="mini">{{item.time}}</el-tag>
                        </div>
                        <div class="msgTime" v-else-if="item.time != msgList[key-1].time">
                            <el-tag type="info" size="mini">{{item.time}}</el-tag>
                        </div>
                        <div :ref="'msgList'+item.msg_id" class="rightMsgItem" v-if="item.from_uid == uid">
                            <el-avatar style="margin-left: 10px;" size="medium" shape="square"
                                       src="{$info.headimg}"></el-avatar>
                            <!-- 文字 -->
                            <div class="msgItemContent">
                                <div style="width: 20px;height: 20px" v-show="item.sendStatus == 'ing'">
                                    <i class="el-icon-loading"></i></div>
                                <i class="el-icon-warning"
                                   style="color: red;margin-right: 5px;cursor:pointer;"
                                   v-show="item.sendStatus == 'error'" @click="resend(item,key)"></i>
                                <el-popover
                                        popper-class="recallClass"
                                        placement="bottom"
                                        trigger="manual"
                                        v-model="item.popoverVisible">
                                    <div style="display: flex;align-items: center;justify-content: space-between;">
                                        <el-link :underline="false" v-if="item.recall_id"
                                                 @click="recallMsg(item)"><i
                                                class="el-icon-refresh-left"></i> 撤回
                                        </el-link>
                                        <el-link :underline="false" type="danger" @click="delMsg(item.msg_id,key)"><i
                                                class="el-icon-error"></i> 删除
                                        </el-link>
                                    </div>
                                    <div class="rightMsgItemBg" v-if="item.type == 1" slot="reference" @contextmenu.prevent="openMenu(key)" v-html="item.content"></div>
                                    <!-- 图片 -->
                                    <el-image
                                            fit="contain"
                                            slot="reference"
                                            v-else-if="item.type == 2"
                                            class="msgImage"
                                            :src="item.content"
                                            :preview-src-list="[item.content]" @contextmenu.prevent="openMenu(key)">
                                    </el-image>
                                    <!-- 语音 -->
                                    <eadmin-audio :mini="true" :url="item.content" v-else-if="item.type == 3" slot="reference" @contextmenu.prevent="openMenu(key)"></eadmin-audio>
                                </el-popover>
                                <div class="rightTriangle" v-if="item.type == 1"></div>
                            </div>

                        </div>
                        <div :ref="'msgList'+item.msg_id" class="leftMsgItem" v-else>
                            <el-avatar style="margin-right: 10px;" size="medium" shape="square"
                                       :src="item.headimg"></el-avatar>
                            <!-- 文字 -->
                            <div class="msgItemContent">
                                <div class="leftTriangle" v-if="item.type == 1"></div>
                                <el-popover
                                        popper-class="recallClass"
                                        placement="bottom"
                                        trigger="manual"
                                        v-model:visible="item.popoverVisible">
                                    <div style="display: flex;align-items: center;justify-content: space-between;">
                                        <el-link :underline="false" type="danger" @click="delMsg(item.msg_id,key)"><i
                                                class="el-icon-error"></i> 删除
                                        </el-link>
                                    </div>
                                    <template #reference>
                                        <div class="leftMsgItemBg" v-if="item.type == 1" @contextmenu.prevent="openMenu(key)" v-html="item.content"></div>
                                        <!-- 图片 -->
                                        <el-image
                                                fit="contain"
                                                @contextmenu.prevent="openMenu(key)"
                                                v-else-if="item.type == 2"
                                                class="msgImage"
                                                :src="item.content"
                                                :preview-src-list="[item.content]">
                                        </el-image>
                                    </template>

                                    <!-- 语音 -->
                                    <!--                                <eadmin-audio :mini="true" :url="item.content" v-else-if="item.type == 3" slot="reference" @contextmenu.prevent="openMenu(key)"></eadmin-audio>-->
                                </el-popover>
                            </div>

                        </div>
                    </div>
                </div>
            </el-scrollbar>
        </div>
        <!-- 发送区 -->
        <div class="sendBox" v-show="nowMsgUid > -1">
            <div class="tools">
                <el-popover placement="top"
                            trigger="click">
                    <div style="display: flex;width: 410px;flex-wrap: wrap;justify-content: space-between">
                        <div v-for="item in emojiArr" class="emoji" @click="sendEmoji(item)">
                            {{item}}
                        </div>
                    </div>
                    <template #reference>
                        <i class="fa fa-smile-o item" style="font-size: 20px"></i>
                    </template>

                </el-popover>
                <i class="el-icon-chat-dot-round item" @click="msgRecord" style="float: right"></i>
            </div>
            <el-scrollbar style="height:90px">
                <div
                        ref="sendInput"
                        contenteditable="true"
                        class="sendTextarea"
                        @keydown.enter="enterClear"
                        @keyup.enter.exact="sendMsg"
                        @keyup.enter.ctrl="sendMsgWrap"
                        @paste="pasteSendInput"
                        @blur="getCursorSelection">
                </div>
            </el-scrollbar>
            <el-popover placement="top-start" content="发送内容不能为空" v-model:visible="sendTipvisible" trigger="manual">
                <el-button size="mini" style="float: right;margin-right: 15px" slot="reference"
                           @click="sendMsg">发送
                </el-button>
            </el-popover>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs} from "vue";
    import im from '../websocket/websocket'
    export default defineComponent({
        name: "ImMain",
        setup(){
            const state = reactive({
                uid:0,
                //聊天内容滚动加载状态
                scrollMsgLoading:false,
                //内容输入框
                sendInput:'',
                //聊天对方昵称
                nowMsgNickname: '',
                //当前聊天对方标示
                nowMsgUid:-1,
                //发送内容
                sendContent:'',
                //发送内容光标位置
                msgSelection:null,
                //聊天内容
                msgList:[],

                sendTipvisible:false,
                //表情
                emojiArr:['😀','😁','😂','😃','😄','😅','😆','😉','😊','😋','😎','😍','😘','😗','😙','😚','😇','😐','😑','😶','😏','😣','😥','😮','😯','😪','😫','😴','😌','😛','😜','😝','😒','😓','😔','😕','😲','😷','😖','😞','😟','😤','😢','😭','😦','😧','😨','😬','😰','😱','😳','😵','😡','😠','🌹','🍀','🍎','💰','📱','🌙','🍁','🍂','🍃','🌷','💎','🔪','🔫','🏀','👄','👍','🔥','💪','👈','👉','👆','👇','👌','👍','👎','✊'],
            })

            im.onMessage((action,data)=>{
                console.log(action)
            })
            //插入表情
            function sendEmoji(emoji){
                if (state.msgSelection && state.sendInput.innerHTML != '') {
                    var textNode = document.createTextNode(emoji);
                    state.msgSelection.insertNode(textNode);
                    state.msgSelection.setStartAfter(textNode);
                }else{
                    state.sendInput.focus()
                    document.execCommand("insertHTML",false,emoji)
                }
            }
            //换行并光标定位末尾
            function sendMsgWrap() {
                 //解决ff不获取焦点无法定位问题
                let range;
                state.sendInput.innerHTML += '<br><br>'
                if (window.getSelection) { //ie11 10 9 ff safari
                    state.sendInput.focus();
                    range = window.getSelection(); //创建range
                    range.selectAllChildren(state.sendInput); //range 选择obj下所有子内容
                    range.collapseToEnd(); //光标移至最后
                } else if (document.selection) { //ie10 9 8 7 6 5
                    range = document.selection.createRange(); //创建选择对象
                    range.moveToElementText(obj); //range定位到obj
                    range.collapse(false); //光标移至最后
                    range.select();
                }
            }
            //禁止textarea回车换行
            function enterClear(event) {
                event.preventDefault()
            }
            //保存光标位置
            function getCursorSelection(){
                state.msgSelection = window.getSelection().getRangeAt(0)
            }
            function pasteSendInput() {

            }
            //发送消息
            function sendMsg() {

            }
            //重发
            function resend() {

            }
            //撤回
            function recallMsg(item) {

            }
            //聊天记录
            function msgRecord() {

            }
            //右键打开文字菜单
            function openMenu(index) {
               // this.popoverVisibleClose()
              //  this.msgList[index].popoverVisible = true
            }
            //判断本地是否已删除记录
            function isDelMsg(msg_id){
                let delMsg = localStorage.getItem('eadmin_del_msg'+state.uid)
                if(delMsg){
                    delMsg = JSON.parse(delMsg)
                }else{
                    delMsg = []
                }
                return delMsg.indexOf(msg_id) > -1
            }
            //删除信息
            function delMsg(msg_id, index) {
                let delMsg = localStorage.getItem('eadmin_del_msg')
                if(delMsg){
                    delMsg = JSON.parse(delMsg)
                }else{
                    delMsg = []
                }
                delMsg.push(msg_id)
                localStorage.setItem('eadmin_del_msg'+state.uid, JSON.stringify(delMsg))
                state.msgList.splice(index, 1)
            }
            return {
                ...toRefs(state),
                sendMsgWrap,
                enterClear,
                sendEmoji,
                getCursorSelection,
                pasteSendInput,
                sendMsg,
                msgRecord,
                isDelMsg,
                openMenu,
                delMsg,
                recallMsg,
                resend
            }
        }
    })
</script>

<style scoped>
    .mainContent{
        height: 100%;
        background: #ffffff;
    }
    .header {
        height: 50px;
        text-indent: 20px;
        line-height: 65px;
        border-bottom: rgba(0,0,0,0.02) solid 1px;
        font-size: 18px;
        color: #000000;
    }
    .msgContentBox {
        height: 470px;
        position: relative;
    }

    .msgTime {
        text-align: center;
    }

    .sendBox {
        border-top: solid 1px #e9e7e6;
        background: #ffffff;
        height: 165px;
        overflow: hidden;
    }
</style>
