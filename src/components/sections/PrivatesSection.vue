<script setup lang="ts">
import { useStore } from "@/stores"
import type { PrivateRepresentative } from "@/types"
import { Helper } from "@/lib/helper"
import { onMounted } from "vue"

const store = useStore()
const reprs = store.project.privateRepresentatives

function createEmptyPrivateSection() {
    const empty = Helper.createPrivateRepresentative()

    reprs.push(empty)
}

onMounted(() => {
    console.log("privates section reprs:", reprs)
})
</script>
<template>
    <template v-for="(repr, idx) in reprs" :key="repr.id">
        <private-section
            :value="repr"
            @update="
                (repr: PrivateRepresentative) => {
                    reprs[idx] = repr
                }
            "
            @remove="reprs.splice(idx, 1)"
        />
    </template>
    <el-button type="primary" @click="createEmptyPrivateSection">
        Добавить представителя по доверенности
    </el-button>
</template>

<style scoped></style>
