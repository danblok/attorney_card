import { createApp } from "vue"
import { createPinia } from "pinia"

import App from "./App.vue"
import router from "./router"
import { ru } from "element-plus/es/locales.mjs"
import ElementPlus from "element-plus"

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(ElementPlus, {
    locale: ru,
})

app.mount("#app")
