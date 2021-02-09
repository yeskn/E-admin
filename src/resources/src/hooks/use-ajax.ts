import request from '/@/utils/axios'
const useAjax =  function (ctx) {
    if (ctx.attrs.url) {
        request({
            url: ctx.attrs.url,
            method: ctx.attrs.method || 'post',
            data: ctx.attrs.params
        }).then((res) => {
            ctx.emit('gridRefresh')
        })
    }
}
export default useAjax
