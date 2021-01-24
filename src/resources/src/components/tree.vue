<template>
    <el-tree :class="[horizontal ? 'eadmin-tree':'']" @check="handleCheckChange" :defaultCheckedKeys="modelValue"></el-tree>
</template>

<script>
    import {defineComponent} from "vue";
    export default defineComponent({
        name: "EadminTree",
        props: {
            modelValue:[Array,Object],
            horizontal:Boolean,
        },
        emits: ['update:modelValue'],
        setup(props, ctx) {
            function handleCheckChange(node,{checkedKeys}){
                ctx.emit('update:modelValue',checkedKeys)
            }
            return {
                handleCheckChange
            }
        }
    })
</script>

<style >
    .eadmin-tree .el-tree-node__content{
        margin-bottom:10px;
    }
    .eadmin-tree .el-tree-node__content span.levelname{
        float:left;

    }
    .eadmin-tree .el-tree-node__children .el-tree-node__children .el-tree-node__content{
        float:left;

    }

</style>