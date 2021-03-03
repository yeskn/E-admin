<template>
    <el-card>
        <template #header>
            <div style="display: flex;align-items: center;justify-content: space-between">
                <el-button-group style="display: flex;align-items: center">
                    <el-button icon="el-icon-back" size="mini" @click="back"></el-button>
                    <div class="breadcrumb">
                        <el-breadcrumb separator-class="el-icon-arrow-right">
                            <el-breadcrumb-item @click="changePath(initPath)" style="cursor: pointer">根目录</el-breadcrumb-item>
                            <el-breadcrumb-item v-for="item in breadcrumb" @click="changePath(item.path)"
                                                :style="item.path ? 'cursor: pointer':''">{{item.name}}
                            </el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                    <el-button icon="el-icon-refresh" size="mini" @click="loading = true"></el-button>
                    <el-button  size="mini" @click="loading = true">上传</el-button>
                    <el-button  size="mini" @click="mkdir">新建文件夹</el-button>
                    <el-button  size="mini" type="danger" @click="loading = true" v-if="selectIds.length > 0">删除选中</el-button>
                </el-button-group>
                <el-col :md="6" style="display: flex;">
                    <!--快捷搜索-->
                    <el-input class="hidden-md-and-down" v-model="quickSearch" clearable prefix-icon="el-icon-search"
                              size="mini" style="margin-right: 10px;flex: 1" placeholder="请输入关键字" @change="loading = true" ></el-input>
                    <el-button class="hidden-md-and-down" type="primary" size="mini" icon="el-icon-search" @click="loading = true">搜索</el-button>
                </el-col>
                <!--                <el-button size="mini">上传</el-button>-->
            </div>
        </template>
        <a-table row-key="path" :row-selection="rowSelection" :columns="tableColumns" :data-source="tableData" :loading="loading">
            <template #name="{ text , record , index }">
                <div class="filename" @click="changePath(record.dir ? record.path:initPath)">
                    <el-image :src="fileIcon(record.dir ? '.dir':text)"
                              style="width: 32px;height: 32px;margin-right: 10px">
                        <div slot="error" style="display: flex; align-items: center;"><i class="el-icon-document"
                                                                                         style="font-size: 32px"/></div>
                    </el-image>
                    {{ text }}
                </div>
            </template>
        </a-table>
    </el-card>

</template>

<script>
    import {computed, defineComponent, reactive, ref, toRefs, triggerRef, unref, watch} from "vue";
    import {deleteArr, fileIcon, unique} from '@/utils'
    import {useHttp} from "@/hooks";
    import {ElMessageBox} from 'element-plus';
    export default defineComponent({
        name: "EadminFileSystem",
        props: {
            data: Array,
            params: Object,
            initPath: String,
        },
        setup(props) {
            const {loading, http} = useHttp()
            const state = reactive({
                tableColumns: [
                    {
                        title: '文件名',
                        dataIndex: 'name',
                        slots: {customRender: 'name'},
                    },
                    {
                        title: '大小',
                        dataIndex: 'size',
                    },
                    {
                        title: '修改时间',
                        dataIndex: 'update_time',
                    },
                    {
                        title: '权限',
                        dataIndex: 'permission',
                    },
                    {
                        title: '所有者',
                        dataIndex: 'author',
                    }
                ],
                tableData: props.data,
                path: props.initPath,
                quickSearch:'',
            })
            watch(() => props.modelValue, (value) => {
                if (value) {
                    loading.value = true
                }
            })
            watch(() => state.path, value => {
                loading.value = true
            })
            watch(loading, (value) => {
                if (value) {
                    loadData()
                }
            })

            function loadData() {
                let requestParams = {
                    search: state.quickSearch,
                    path: state.path,
                    ajax_request_data: 'page',
                }
                requestParams = Object.assign(requestParams, props.params)
                http({
                    url: '/eadmin.rest',
                    params: requestParams
                }).then(res => {
                    state.tableData = res.data
                }).finally(() => {

                })
            }

            //改变目录
            function changePath(path) {
                if (path) {
                    state.path = path
                }
            }
            //后退
            function back() {
                const arr = state.path.split('/').filter(item => {
                    return item
                })
                const initPath = props.initPath.split('/').filter(item => {
                    return item
                })
                arr.pop()
                if (arr.length >= initPath.length) {
                    state.path = '/' + arr.join('/')
                }
            }
            //新建文件夹
            function mkdir() {
                ElMessageBox.confirm('新建文件夹','',{showInput:true}).then(({value})=>{
                    const {http} = useHttp()
                    http({
                        url:'filesystem/mkdir',
                        method:'post',
                        data:{
                            path:state.path+'/'+value
                        }
                    }).then(res=>{
                        loadData()
                    })
                })
            }
            const breadcrumb = computed(() => {
                const arr = state.path.split('/').filter(item => {
                    return item
                })
                const initPath = props.initPath.split('/').filter(item => {
                    return item
                })
                const breadcrumb = []
                let paths = []
                let path = ''
                for (let i = 0; i < arr.length; i++) {
                    paths = []
                    for (let k = 0; k <= i; k++) {
                        paths.push(arr[k])
                    }
                    if (paths.length < initPath.length) {
                        path = ''
                    } else {
                        path = '/' + paths.join('/')
                    }
                    breadcrumb.push({
                        name: arr[i],
                        path: path,
                    })
                }
                return breadcrumb
            })
            const selectIds = ref([])
            const rowSelection = computed(() => {
                if (props.hideSelection) {
                    return null
                } else {
                    return {
                        selectedRowKeys: unref(selectIds),
                        //当用户手动勾选数据行的 Checkbox 时触发的事件
                        onSelect: (record, selected, selectedRows, nativeEvent) => {
                            const ids = selectedRows.map(item => {
                                return item.path
                            })
                            if (selected) {
                                selectIds.value = unique(selectIds.value.concat(ids))
                            } else {
                                deleteArr(selectIds.value, record.path)
                            }

                        },
                        onSelectAll: (selected, selectedRows, changeRows) => {
                            const ids = selectedRows.map(item => {
                                return item.path
                            })
                            if (selected) {
                                selectIds.value = unique(selectIds.value.concat(ids))
                            } else {
                                changeRows.map(item => {
                                    deleteArr(selectIds.value, item.path)
                                })
                            }
                        },
                    }
                }
            })
            return {
                back,
                mkdir,
                selectIds,
                breadcrumb,
                changePath,
                loading,
                rowSelection,
                fileIcon,
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>
    .filename {
        display: flex;
        align-items: center;
        height: 35px;
        cursor: pointer;
    }

    .breadcrumb {
        height:28px;
        background-color: #f3f3f3;
        padding: 0 10px;
        display: flex;
        align-items: center;
        border: solid 1px #cccccc;
    }
</style>
