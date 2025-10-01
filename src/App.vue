<script setup lang="ts">
import { onMounted } from "vue"
import { useStore } from "./stores"
import { ElNotification } from "element-plus"

const store = useStore()

async function getProjectId() {
    return new Promise<string>((res) => {
        //@ts-expect-error
        if (BX24) {
            //@ts-expect-error
            BX24.init(function () {
                //@ts-expect-error
                console.log("Placement:", BX24.placement.info())
                //@ts-expect-error
                res(BX24.placement.info().options.ID)
            })
        } else {
            console.log("New attorney")
            res("0")
        }
    })
}

onMounted(async () => {
    console.log("init fetching...")
    // Подргружаем ID доверенности или возвращаем пустой ID
    const projectId = await getProjectId()
    store.project.id = projectId

    await Promise.all([
        store.fetchRepresentatives(),
        store.fetchCompanies(),
        store.fetchContacts(),
        store.fetchForms(),
        store.fetchSubjects(),
        store.fetchSystems(),
        store.fetchTemplates(),
        store.fetchStatuses(),
        store.fetchCurrentUser(),
        store.fetchActivityAreas(),
        store.fetchAuthorities(),
        store.fetchDirector(),
        store.fetchUsers(),
        projectId !== "0"
            ? store.fetchProject(store.project.id)
            : Promise.resolve(), // Не делаем запрос, если создаём новую доверенность
    ]).then(() => {
        if (!store.project.attorneyData.executiveAuthority?.id) {
            store.project.attorneyData.executiveAuthority = store.director
        }
        if (store.project.attorneyData.initiator.id.startsWith("UM")) {
            store.project.attorneyData.initiator = store.currentUser
        }

        store.loadingStatus = "finished"
    })
})
</script>

<template>
    <div class="wrapper h-screen" v-loading="store.loadingStatus === 'loading'">
        <tabs v-if="store.loadingStatus !== 'loading'"></tabs>
        <!-- <h1 class="text-center align-middle my-auto"> -->
        <!--     Осталось совсем немного... -->
        <!-- </h1> -->
    </div>
</template>
