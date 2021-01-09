<template>
    <el-button @click="open">da</el-button>
    <el-table v-loading="loading" :data="tableData" v-bind="$attrs">
        <el-table-column v-for="column in columns" v-bind="column">
            <template #header>
                <render :data="column.header"></render>
            </template>
            <template #default="scope">
                <render :data="scope.row[column.prop]"></render>
            </template>
        </el-table-column>
    </el-table>
    <el-pagination v-if="pagination" class="pagination" v-bind="pagination"></el-pagination>
</template>

<script>
    import {defineComponent,ref} from "vue";
    import render from "/@/components/render.vue"
    export default defineComponent({
        name: "EadminGrid",
        components: {
            render,
        },
        props:{
            data:Array,
            columns:Array,
            pagination:[Object,Boolean],
        },
        inheritAttrs:false,
        setup(props){
            const loading = ref(false)
            const tableData = props.data
            function open() {
                this.$alert('这是一段内容', '标题名称', {
                    confirmButtonText: '确定',
                    callback: action => {
                        this.$message({
                            type: 'info',
                            message: `action: ${ action }`
                        });
                    }
                });
            }
            return {
                open,
                loading,
                tableData
            }
        }
    })
</script>

<style scoped>
.pagination{
    background: #fff;
    padding: 10px 16px;
    border-radius: 4px;
}
</style>
