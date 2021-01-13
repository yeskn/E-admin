import request from '/@/utils/axios'
import {ref} from "vue";
const useHttp =  function () {
    const loading = ref(false)
    const http =  function (params) {
        return new Promise((resolve, reject) =>{
            loading.value = true
            request(params).then(res=>{
                resolve(res)
            }).catch(res=>{
                reject(res)
            }).finally(()=>{
                loading.value = false
            })
        })
    }
    return {loading,http}
}
export default useHttp
