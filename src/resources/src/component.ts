import app from  './app'
import form from './components/form/form.vue'
import manyItem from './components/form/manyItem.vue'
import dialog from './components/dialog.vue'
import drawer from './components/drawer.vue'
import eadminErrorPage from './components/EadminErrorPage.vue'
app.component(form.name,form)
app.component(manyItem.name,manyItem)
app.component(dialog.name,dialog)
app.component(drawer.name,drawer)
app.component(eadminErrorPage.name,eadminErrorPage)
