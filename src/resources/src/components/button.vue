<template>
    <el-button @click="clickHandel"><slot></slot></el-button>
</template>

<script>
    import {defineComponent} from 'vue'
    import request from '/@/utils/axios'
    export default defineComponent({
        name: "EadminButton",
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
        emits: ['gridRefresh'],
        setup(props, ctx) {
            function clickHandel() {
                if (props.url) {
                    let params = props.params
                    request({
                        url: props.url,
                        method: props.method,
                        data: params
                    }).then((res) => {
                        ctx.emit('gridRefresh')

                    })
                }
            }
            return {
                clickHandel
            }
        }
    })
</script>

<style scoped>

</style>
