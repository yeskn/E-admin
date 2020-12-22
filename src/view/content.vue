<template>
    <div class="eadmin-container">
        <!--{notempty name="title"}-->
        <div class="container-header">
            <span class="title">{$title|raw}</span>
            <eadmin-breadcrumb style="margin-left: auto"></eadmin-breadcrumb>
        </div>
        <!--{/notempty}-->
        {$html|raw}
    </div>
</template>

<script>
    export default {
        name: "eadminContent",
        data(){
            return {
                tableDataUpdate:false,
                tableData:[],
                {$scriptVar|raw|default=''}
            }
        },
        methods:{
            linkComponent(url,name){
                this.$request({
                    url: url,
                    eadmin_component:true,
                }).then(res=>{
                    this[name] = () => new Promise(resolve => {
                        resolve(this.$splitCode(res.data))
                    })
                })
            }
        }
    }
</script>

<style scoped>
    .eadmin-container .container-header{
        display: flex;
        align-items: center;
        height: 50px;
    }
    .eadmin-container .container-header .title{
        font-size: 20px;
        font-weight: 400;
        color: #2c2c2c;
        display: flex;
        align-items: center;
    }
    .eadmin-container .el-row+.el-row{
        margin-top: 15px;
    }
</style>
