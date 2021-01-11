import { ref } from 'vue';
import request from '/@/utils/axios'
const loading = ref(false)
const http =  function (params,loading) {
    return new Promise((resolve, reject)=> {
        loading.value = true
        request(params).then(res=>{
            resolve(res)
        }).finally(()=>{
            loading.value = false
        })
    })
}
export {loading,http}
