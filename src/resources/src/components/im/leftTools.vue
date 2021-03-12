<template>
    <div class="leftTools">
        <div class="item">
            <el-avatar size="medium" shape="square" :src="headimg"></el-avatar>
        </div>
        <div class="item" v-for="item in leftToos">
            <el-tooltip effect="light" :content="item.tip" placement="right">
                <el-badge type="danger" :value="item.num" :max="99" v-if="item.num > 0">
                    <i :class="item.icon" @click="checkedLeftTools = item.mark"
                       :style="{color:(checkedLeftTools == item.mark?'#409EFF':'')}"></i>
                </el-badge>
                <i v-else :class="item.icon" @click="checkedLeftTools = item.mark"
                   :style="{color:(checkedLeftTools == item.mark?'#409EFF':'')}"></i>
            </el-tooltip>
        </div>
    </div>
</template>

<script>
    import {defineComponent,reactive,toRefs,watch} from "vue";
    import im from "@/components/im/websocket/websocket";

    export default defineComponent({
        name: "ImLeftTools",
        props:{
            modelValue:String,
            //头像
            headimg:String,
            //好友数量
            unReadFriendNum:Number,
            //待接入
            customerConnNum:Number,
            //未读数量
            unReadNum:Number,
        },
        emits:['update:modelValue'],
        setup(props,ctx){

            const state = reactive({
                //工具栏
                leftToos: [
                    {
                        icon: 'el-icon-s-comment',
                        tip: '会话',
                        mark: 'message',
                        num:props.unReadNum,
                    },
                    {
                        icon: 'el-icon-user-solid',
                        tip: '通讯录',
                        mark: 'friend',
                        num:props.unReadFriendNum,
                    },
                    {
                        icon: 'el-icon-place',
                        tip: '待接入',
                        mark: 'customer',
                        num:props.customerConnNum,
                    },
                ],
                //当前选中工具栏
                checkedLeftTools: 'message',
            })
            watch(()=>props.modelValue,val=>{
                state.checkedLeftTools = val
            })
            watch(()=>state.checkedLeftTools,val=>{
                ctx.emit('update:modelValue',val)
            })
            watch(()=>props.unReadNum,val=>{
                state.leftToos[0].num = val
            })
            watch(()=>props.unReadFriendNum,val=>{
                state.leftToos[1].num = val
            })
            watch(()=>props.customerConnNum,val=>{
                state.leftToos[2].num = val
            })

            return {
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>

    .leftTools {
        background-color: #1a1a1a;
        width: 80px;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .leftTools .item {
        margin-top: 20px;
    }

    .leftTools .item i {
        font-size: 22px;
        cursor: pointer;


    }
</style>
