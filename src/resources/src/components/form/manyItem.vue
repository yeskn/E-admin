<template>
    <el-divider content-position='left'>{{title}}</el-divider>
    <div v-for="(item,index) in value">
        <slot></slot>
        <el-form-item>
            <el-button size="mini" v-if="value.length - 1 == index" type='primary' plain @click="add">新增</el-button>
            <el-button size="mini" type='danger' v-show='value.length > 1' @click="remove(index)">移除</el-button>
            <el-button size="mini" @click="handleUp(index)" v-show='value.length > 1 && index > 0'>上移</el-button>
            <el-button size="mini" v-show='value.length > 1 && index < value.length-1' @click="handleDown(manyIndex)">下移</el-button>
        </el-form-item>
    </div>
</template>

<script>
    import {defineComponent,reactive,watch} from "vue";
    export default defineComponent({
        name: "EadminManyItem",
        props: {
            title:String,
            modelValue: Array
        },
        emits:['update:modelValue'],
        setup(props,ctx){
            const value = reactive(props.modelValue)
            watch(value,(val)=>{
                ctx.emit('update:modelValue',val)
            })
            // 上移
            function handleUp (index) {
                const len = value[index - 1]
                value[index - 1] = index
                value[index] = len
            }
            //添加元素
            function add(manyData){
                value.push({})
            }
            //移除元素
            function remove(index){
                value.splice(index, 1)
            }
            // 下移
            function handleDown (index) {
                const len = value[index + 1]
                value[index + 1] = index
                value[index] = len
            }
            return {
                value,
                add,
                remove,
                handleUp,
                handleDown,
            }
        }
    })
</script>

<style scoped>

</style>
