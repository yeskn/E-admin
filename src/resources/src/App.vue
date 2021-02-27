<template>
    <router-view/>
    <eadmin-error-page/>
</template>

<script>
    import {defineComponent} from "vue";
    import resize from "@/hooks/use-resize"
    export default defineComponent({
        name: 'App',
        setup() {
            resize()
        }
    })
</script>
