<template>
    <span @click="opend">
        <slot></slot>
    </span>
</template>

<script>
    import {defineComponent} from 'vue'
    import request from '@/utils/axios'
    import {ElMessageBox} from 'element-plus';

    export default defineComponent({
        name: "EadminConfirm",
        props: {
            //请求url
            url: String,
            //请求method
            method: {
                type: String,
                default: 'post'
            },
            //请求参数
            params: Object,
        },
        emits: ['confirm', 'cancel','gridRefresh'],
        setup(props, ctx) {
            function opend() {
                ElMessageBox.confirm(props.message, props.title, ctx.attrs)
                    .then(({value}) => {
                        if (props.url) {
                            let params = props.params
                            if (value) {
                                params = Object.assign(params, {inputValue: value})
                            }
                            request({
                                url: props.url,
                                method: props.method,
                                data: params
                            }).then((res)=>{
                                ctx.emit('gridRefresh')
                                ctx.emit('confirm')
                            })
                        }else{
                            ctx.emit('gridRefresh')
                            ctx.emit('confirm')
                        }
                    }).catch((action) => {
                         ctx.emit('cancel')
                    })
            }

            return {
                opend
            }
        }
    })
</script>

<style scoped>

</style>
