<script>
    import {defineComponent, computed, toRaw, h, resolveComponent, inject,isProxy} from 'vue'
    import {store} from '/@/store'
    import {splitCode} from '/@/utils/splitCode'
    import dayjs from 'dayjs'
    export default defineComponent({
        name: "EadminRender",
        props: {
            data: {
                type: [String, Number, Array, Object],
                default: '',
            },
            slotProps:Object
        },
        render() {
            return this.render
        },
        setup(props,ctx) {
            const state = inject(store)
            const modelValue = state.proxyData
            const renderComponent = (data, slotProps) => {

                if(!data.attribute){
                    return
                }
                let expression, children = {}, name, attribute
                //属性绑定
                for (let bindAttr in data.bindAttribute) {
                    expression = 'data.attribute[bindAttr] = modelValue.' + data.bindAttribute[bindAttr]
                    eval(expression)
                }
                //双向绑定值
                if (data.bindAttribute && data.bindAttribute.modelValue) {
                    let field = data.bindAttribute.modelValue
                    // 本次渲染是循环属性
                    if (slotProps && slotProps.row) {
                        data.attribute.modelValue = slotProps.row[field]
                        data.attribute['onUpdate:modelValue'] = value => {
                            if(data.attribute.valueFormat){
                                //时间特殊处理
                                if(value == null){
                                    slotProps.row[data.bindAttribute.startField] = null
                                    slotProps.row[data.bindAttribute.endField] = null
                                }else{
                                    value = dateFormat(value,data.attribute.valueFormat)
                                    if(data.attribute.hasOwnProperty('startField') && data.attribute.hasOwnProperty('endField')){
                                        slotProps.row[data.bindAttribute.startField] = value[0]
                                        slotProps.row[data.bindAttribute.endField] = value[1]
                                    }
                                }

                            }
                            slotProps.row[field] = value
                        }
                    } else {
                        expression = 'data.attribute.modelValue = modelValue.' + field
                        eval(expression)
                        data.attribute['onUpdate:modelValue'] = value => {
                            if(data.attribute.valueFormat){
                                //时间特殊处理
                                if(value == null){
                                    expression = 'modelValue.' + data.bindAttribute.startField + ' = null'
                                    eval(expression)
                                    expression = 'modelValue.' + data.bindAttribute.endField + ' = null'
                                    eval(expression)
                                }else{
                                    value = dateFormat(value,data.attribute.valueFormat)
                                    if(data.attribute.hasOwnProperty('startField') && data.attribute.hasOwnProperty('endField')){
                                        expression = 'modelValue.' + data.bindAttribute.startField + ' = value[0]'
                                        eval(expression)
                                        expression = 'modelValue.' + data.bindAttribute.endField + ' = value[1]'
                                        eval(expression)
                                    }
                                }
                            }
                            expression = 'modelValue.' + field + ' = value'
                            eval(expression)
                        }
                    }
                }
                //事件绑定
                for (let event in data.event) {
                    let eventBind = data.event[event]
                    if(event === 'GridRefresh' && slotProps && slotProps.grid){
                        //grid刷新事件绑定
                        data.attribute.onGridRefresh = (e)=>{

                            modelValue[slotProps.grid] = true
                        }
                    }else{
                        data.attribute['on'+event] = (e)=>{
                            for (let field in eventBind) {
                                expression = 'modelValue.' + field + ' = eventBind[field]'
                                eval(expression)
                            }
                        }
                    }
                }

                if(!data.attribute.slotProps && slotProps){
                    data.attribute.slotProps = slotProps
                }
                //插槽名称对应内容
                for (let slot in data.content) {
                    children[slot] = (scope) => {
                        if (!isProxy(scope) && (JSON.stringify(scope) === '{}' || scope === undefined)) {
                            scope = slotProps
                        }
                        return userRender(data.content[slot], scope)
                    }
                }

                attribute = {...data.attribute}
                if(data.name == 'html'){
                    return h('span', attribute, children)
                }else if(data.name == 'component'){
                    return h(splitCode(data.content.default[0]))
                }
                name = resolveComponent(data.name)
                console.log(name)
                //for 遍历中的 ElFormItem 验证prop error处理
                if(data.name == 'ElFormItem'){
                    if(slotProps && slotProps.propField){
                        if(!modelValue[slotProps.validator][slotProps.propField][slotProps.$index]){
                            modelValue[slotProps.validator][slotProps.propField][slotProps.$index] = {}
                        }
                        let propField = attribute.prop
                        attribute.prop = slotProps.propField + '.' + slotProps.$index+ '.' + propField
                        attribute.error = modelValue[slotProps.validator][slotProps.propField][slotProps.$index][propField]
                    }
                }
                if (data.map.bindName) {
                    let field = data.map.bindName
                    return modelValue[field].map(item => {
                        for (let attr in data.map.attribute) {
                            attribute[attr] = item[data.map.attribute[attr]]
                        }
                        let mapAttribute = {...attribute}
                        if(mapAttribute.slotDefault){
                            children.default = ()=> mapAttribute.slotDefault
                        }
                        let mapChildren = {...children}
                        return h(name, mapAttribute, mapChildren)
                    })
                } else {

                    return h(name, attribute, children)
                }
            }
            //日期格式格式化 value日期,format格式
            function dateFormat(value,format){
                if(Array.isArray(value)){
                    value = value.map(item=>{
                        return dayjs(item).format(format)
                    })
                }else{
                    value = dayjs(value).format(format)
                }
                return value
            }
            function userRender(slot, scope) {

                return slot.map(item => {
                    if (typeof (item.where) == 'object' && (item.where.AND.length > 0 || item.where.OR.length > 0)) {
                        // //条件if渲染实现
                        let expression = whereCompile(item.where.AND, item.where.OR,scope)
                        if (typeof (item) == 'object') {
                            expression = expression + ' ? renderComponent(item,scope) : null'
                        } else {
                            expression = expression + ' ? h({setup(){return {...modelValue}},template:item}) : null'
                        }
                        return eval(expression)
                    } else {
                        if (typeof (item) == 'object') {
                            return renderComponent(item, scope)
                        } else {
                            return h({
                                setup() {
                                    return {
                                        ...modelValue
                                    }
                                },
                                template: `${item}`
                            })
                        }
                    }
                })

            }

            /**
             * 合并where
             * @param whereAnd
             * @param whereOr
             * @returns {string}
             */
            function whereCompile(whereAnd, whereOr,scope) {
                let expressionStr = parseWhere(whereAnd, 'AND',scope)
                let expressionOr = parseWhere(whereOr, 'OR',scope)
                if (expressionStr && expressionOr) {
                    expressionStr += ' || ' + expressionOr
                } else if (expressionOr) {
                    expressionStr = expressionOr
                }
                return expressionStr
            }

            /**
             * 解析where
             * @param wheres
             * @param op AND | OR
             * @returns {string}
             */
            function parseWhere(wheres, op,scope) {
                let evals = []
                let expression = ''

                wheres.forEach((where, index) => {
                    if (where.where) {
                        let expressionStr = whereCompile(where.where.AND, where.where.OR,scope)
                        evals.push("(" + expressionStr + ")")
                    } else {

                        let  val
                        if(scope && scope.row){
                            val = eval('scope.row.' + where.field)
                        }else{
                            val = eval('modelValue.' + where.field)
                        }
                        if(Array.isArray(val)){
                            if(scope && scope.row){
                                if(where.op == 'notIn'){
                                    evals.push('(scope.row.' + where.field+".indexOf('"+where.condition+"') == -1 && scope.row."+ where.field+".indexOf("+where.condition+") == -1)")
                                }else{
                                    evals.push('(scope.row.' + where.field+".indexOf('"+where.condition+"') >= 0 || scope.row."+ where.field+".indexOf("+where.condition+") >= 0)")
                                }
                            }else{
                                if(where.op == 'notIn'){
                                    evals.push('(modelValue.' + where.field+".indexOf('"+where.condition+"') == -1 && modelValue."+ where.field+".indexOf("+where.condition+") == -1)")
                                }else{
                                    evals.push('(modelValue.' + where.field+".indexOf('"+where.condition+"') >= 0 || modelValue."+ where.field+".indexOf("+where.condition+") >= 0)")
                                }
                            }

                        }else{
                            let operator = where.op
                            if(where.op == 'notIn'){
                                operator = '!='
                            }
                            evals.push("'" + val + "' " + operator + ' ' + "'" + where.condition + "'")
                        }
                    }
                })
                if (op == 'AND') {
                    op = '&&'
                } else {
                    op = '||'
                }
                expression += evals.join(' ' + op + ' ')
                console.log(expression)
                return expression
            }
            //赋值方法
            function setProxyData(data){
                for(let field in data.bind){
                    if(!modelValue.hasOwnProperty(field)){
                        modelValue[field] = data.bind[field]
                    }
                }
                for(let slot in data.content){
                    data.content[slot].forEach(item=>{
                        if(typeof(item) == 'object'){
                            setProxyData(item)
                        }
                    })
                }
            }
            const render = computed(() => {
                if (props.data) {
                    setProxyData(props.data)
                    const jsonRender = toRaw(props.data)
                    return renderComponent(jsonRender,props.slotProps)
                } else {
                    return null
                }
            })
            return {
                render
            }
        },
    })
</script>
