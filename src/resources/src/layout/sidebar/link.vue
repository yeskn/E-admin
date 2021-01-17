<template>
    <span @click="link()">
        <slot></slot>
    </span>
</template>

<script>
    import router from '/@/router'

    import {defineComponent,resolveComponent,h} from 'vue'
    export default defineComponent({
        name: "Eadminlink",
        props:{
            url:String,
        },
        setup(props){
            const url = props.url
            function link(){
                if (isExternal(url)) {
                    window.open(url)
                }else{
                    router.push(url)
                }
            }
            return {
                link
            }
        }
    })
</script>

<style scoped>

</style>