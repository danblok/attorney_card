<script setup lang="ts">
import { inject, ref } from "vue"
import { useFormStore } from "@/stores"
import type { PrivateRepresentative } from "@/types"

const formStore = useFormStore()
const form = formStore.form
const formRef = inject("form-ref")

function createEmptyPrivateSection() {
    const empty: PrivateRepresentative = {
        id: 0,
        reprId: 0,
        isGardiaEmployee: false,
        firstName: "",
        lastName: "",
        secondName: "",
        position: "",
        passportIssuedBy: "",
        passportIssuedDate: new Date().toDateString(),
        divisionCode: "",
        registrationAddress: "",
        additionalInfo: "",
        passportSeries: "",
        passportNumber: "",
    }

    form.privateRepresentatives.push(empty)
}
</script>
<template>
    <template v-for="(repr, idx) in form.privateRepresentatives" :key="repr.id">
        <private-section
            v-model="form.privateRepresentatives[idx]"
            @remove="form.privateRepresentatives.splice(idx, 1)"
        />
    </template>
    <el-button type="primary" @click="createEmptyPrivateSection"
        >Добавить представителя по доверенности</el-button
    >
</template>

<style scoped></style>
