<template>
        <el-main ref="ruleForm" style="background: #fff;border-radius: 4px;">
            <el-form ref="form" @submit.native.prevent :model="form" {$attrStr|raw}>
                {$formItem|raw}
                <el-form-item :style="{textAlign:'{$sumbitAlign|default=\'left\'}' }">
                    <!--{if isset($prependSubmitExtend)}-->
                    {$prependSubmitExtend|raw}
                    <!--{/if}-->
                    <!--{if !isset($hideSubmitButton)}-->
                    <el-button type="primary" native-type="submit" :loading="loading" @click="loading = true">{$submitText|default='保存数据'}</el-button>
                    <!--{/if}-->
                    <!--{if !isset($hideResetButton)}-->
                    <el-button @click="resetForm('form')">重置</el-button>
                    <!--{/if}-->
                    <!--{if isset($appendSubmitExtend)}-->
                    {$appendSubmitExtend|raw}
                    <!--{/if}-->
                </el-form-item>
            </el-form>
        </el-main>
</template>

<script>
    export default {
        inject: ['reload'],
        props:{
            dialogVisible:Boolean,
            tableDataUpdate:Boolean,
            //表单数据是否已保存
            dataIsSave:Boolean,
            refresh: {
                type: String,
                default: '0'
            },
        },
        data(){
            let _self = this
            return {
                tableData:[],
                loading:false,
                auto:'',
                manyIndex:0,
                plugIframe:null,
                iframeVisible:false,
                iframeField:null,
                formItemTags:[],
                closeVisible:true,
                closeWindow:false,
                validates:[],
                watchOldValue:[],
                form:{$formData|raw},
                formTags:{$formTags|raw},
                {$formScriptVar|raw}
            }
        },
        watch:{
            loading(val){
                if(val){
                    this.onSubmit()
                }
            },
            form:{
              handler(val) {
                  this.$emit('update:dataIsSave', false)
              },
              deep:true
            },
            //监听
            {$watchJs|raw}
            //
        },
        created(){
            this.init()
        },
        computed: {
            labelPosition() {
                if(this.$store.state.app.device === 'mobile'){
                    return 'top'
                }else{
                    return 'right'
                }
            },
        },
        methods:{
            interactChangeInit(){
                {$interactChangeInitJs|raw|default=''}
            },
            //互动切换事件
            interactChange(val,tag,manyIndex,changeType=''){
                {$interactChangeJs|raw|default=''}
            },
            init(){
                {$script|raw}
                this.interactChangeInit()
            },
            //数组寻找并删除
            deleteArr(arr,value){
                for(var i = arr.length ; i > 0 ; i--){
                    if(arr[i-1]== value){
                        arr.splice(i-1,1);
                    }
                }
            },
            // 一对多上移
            handleUp (relation,index) {
                const len = this.form[relation][index - 1]
                this.$set(this.form[relation], index - 1, this.form[relation][index])
                this.$set(this.form[relation], index, len)
            },
            // 一对多下移
            handleDown (relation,index) {
                const len = this.form[relation][index + 1]
                this.$set(this.form[relation], index + 1, this.form[relation][index])
                this.$set(this.form[relation], index, len)
            },

            //一对多添加元素
            addManyData(relation,manyData){
                this.form[relation].push(JSON.parse(decodeURIComponent(manyData)))
                this.init()
            },
            //一对多移除元素
            removeManyData(relation,index){
                this.form[relation].splice(index, 1)
                this.init()
            },
            //移除错误
            clearValidate(formName) {
                this.$refs[formName].clearValidate();
                this.validates[formName+'ErrorMsg'] = ''
            },
            //一对多移除错误
            clearValidateArr(relation,formName,index){
                this.$refs[formName][index].clearValidate();
                this.validates[relation+'_'+formName+'ErrorMsg'] = ''
            },
            handleCheckChange(data){
                let field = this.$refs.tree.$attrs.field
                this.form[field] = this.$refs.tree.getCheckedNodes();
            },
            //提交
            onSubmit(){
                let url,method
                let urlArr = this.$route.path.split('/')
                //url = urlArr[1]+'/'+ urlArr[2]
                url = '{$submitUrl}'
                if(this.form.id == undefined){
                    url = url+'.rest'
                    method = 'post'
                }else{
                    url = url +'/'+this.form.id+'.rest'
                    method = 'put'
                }
                this.$emit('update:tableDataUpdate', false)
                this.$request({
                    url: url,
                    method: method,
                    data:this.form,
                    params:this.$route.query
                }).then(response=>{
                    this.loading = false
                    if(response.code == 200){
                        if(this.closeVisible){
                            this.$emit('update:dialogVisible', false)
                            this.$emit('update:tableDataUpdate', true)
                        }
                        if(this.closeWindow){
                            window.close()
                        }
                        if (this.refresh == 1) {
                            this.reload()
                        }
                    }else if(response.code == 422){

                        let index
                        if(response.index){
                            index = response.index
                        }else{
                            index = ''
                        }
                        for(field in response.data){
                            val = response.data[field]
                            field = field.replace('.','_')
                            this.validates[field+index+'ErrorMsg'] = val
                        }
                        if(this.$refs[field]){
                            this.$refs[field].$el.scrollIntoView({　　//滚动到指定节点
                                block: 'center',　　　　　//值有start,center,end，nearest，当前显示在视图区域中间
                                behavior: 'smooth'　　　　//值有auto、instant,smooth，缓动动画（当前是慢速的）
                            })
                        }
                    }
                }).catch(res=>{
                    this.loading = false
                })
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            }
        }
    }
</script>

<style scoped>
    {$styleHorizontal|raw|default=''}
</style>
