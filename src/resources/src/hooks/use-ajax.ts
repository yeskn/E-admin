import request from '@/utils/axios'
const useAjax =  function ( ctx :any) {
    if (ctx.attrs.url) {
        request({
            url: ctx.attrs.url,
            method: ctx.attrs.method || 'post',
            data: ctx.attrs.params
        }).then((res:any) => {
            ctx.emit('gridRefresh')
        })
    }
}
export default useAjax
