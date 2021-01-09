<template>
    <el-table :data="tableData" v-bind="$attrs">
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
    import {defineComponent} from "vue";
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
        setup(props,ctx){
            const tableData = props.data
            return {
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
