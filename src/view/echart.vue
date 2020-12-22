<template>
    <el-card shadow="hover">
        <div slot="header" >
            <el-row style="display: flex;align-items: center">
                <!--{notempty name="title"}-->
                    <el-col :xs="24" :sm="24" :md="4" :span="4" >
                        <div>{$title}</div>
                    </el-col>
                <!--{/notempty}-->
                <el-col :xs="24" :sm="24" :md="20" :span="20" style="margin-left: auto">
                    <el-button-group >

                        <el-button v-if="params.date_type == 'yesterday'" size="small" type="primary" @click="requestData('yesterday')">昨天</el-button>
                        <el-button v-else plain size="small" @click="requestData('yesterday')">昨天</el-button>

                        <el-button v-if="params.date_type == 'today'" size="small" type="primary" @click="requestData('today')">今天</el-button>
                        <el-button v-else plain size="small" @click="requestData('today')">今天</el-button>

                        <el-button v-if="params.date_type == 'week'" size="small" type="primary" @click="requestData('week')">本周</el-button>
                        <el-button v-else plain size="small" @click="requestData('week')">本周</el-button>

                        <el-button v-if="params.date_type == 'month'" size="small" type="primary" @click="requestData('month')">本月</el-button>
                        <el-button v-else plain size="small" @click="requestData('month')">本月</el-button>

                        <el-button v-if="params.date_type == 'year'" size="small" type="primary" @click="requestData('year')">全年</el-button>
                        <el-button v-else plain size="small" @click="requestData('year')">全年</el-button>

                        <el-date-picker
                                class="hidden-md-and-down"
                                size="small"
                                v-model="rangeDate"
                                type="daterange"
                                value-format="yyyy-MM-dd"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                        >
                        </el-date-picker>
                        <el-date-picker
                                class="hidden-md-and-up"
                                size="small"
                                v-model="rangeDate"
                                type="daterange"
                                value-format="yyyy-MM-dd"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                                style="width: 275px;margin-top: 5px"
                        >
                        </el-date-picker>
                    </el-button-group>
                </el-col>
            </el-row>

        </div>

            <!--{notempty name="$filter"}-->
            <el-form class="hidden-md-and-down" :inline="true" size="small" ref="form" @submit.native.prevent :model="form">
                {$filter|raw|default=''}
                <!-- PC端-->
                <el-button class="hidden-md-and-down filter-item" size="small" type="primary" icon="el-icon-search" @click="handleFilter">搜索</el-button>
                <el-button class="hidden-md-and-down filter-item" size="small" icon="el-icon-refresh" @click="filterReset">重置</el-button>

            </el-form>
            <el-form class="hidden-md-and-up" size="mini" ref="form" @submit.native.prevent :model="form">
                {$filter|raw|default=''}
                <!-- 移动端-->
                <el-button class="hidden-md-and-up filter-item" size="mini" type="primary" icon="el-icon-search" @click="handleFilter"></el-button>
                <el-button class="hidden-md-and-up filter-item" size="mini" icon="el-icon-refresh" @click="filterReset"></el-button>
            </el-form>
            <!--{/notempty}-->
            <component v-loading="loading" :is="component"></component>
    </el-card>
</template>

<script>
    export default {
        data(){
            return {
                form:{},
                rangeDate:[],
                component:null,
                loading:false,
                params:{
                    date_type:'today'
                },
                {$tableScriptVar|raw|default=''}
            }
        },
        watch:{
            rangeDate(val){
                if(val == null){
                    this.requestData('today')
                }else{
                    this.params.start_date = val[0]
                    this.params.end_date = val[1]
                    this.requestData('range')
                }

            }
        },
        created(){
            this.component = () => new Promise(resolve => {
                resolve(this.$splitCode(decodeURIComponent("{$html|raw}")))
            })
        },
        methods:{
            filterColumnChange(){},
            clearValidate(){},
            //查询过滤
            handleFilter(){
                this.params = Object.assign(this.params,this.form)
                this.requestData(this.params.date_type)
            },
            //重置筛选表单
            filterReset(){
                this.$refs['form'].resetFields();
            },
            requestData(type){
                this.loading = true
                this.params.date_type = type
                this.params.ajax = true
                /*{foreach :request()->param() as $key=>$value}*/
                /*{php}if(is_array($value))continue;{/php}*/
                this.params['{$key}'] = '{$value}'
                /*{/foreach}*/
                this.$request({
                    url: '{$url|default=""}',
                    params:this.params
                }).then(res=>{
                    this.component = () => new Promise(resolve => {
                        this.loading = false
                        resolve(this.$splitCode(res.data))
                    })
                }).catch(res=>{
                    this.loading = false
                })
            }
        }
    }
</script>

<style scoped>

</style>
