<template>
    <el-form-item>
        <el-form-item
            prop="representativeType"
            label="Представитель по доверенности"
            required
        >
            <el-select
                v-model="form.representativeType"
                :options="options"
                clearable
                filterable
            >
            </el-select>
        </el-form-item>
    </el-form-item>
    <component
        :is="
            form.representativeType === 'legal' ? LegalSection : PrivatesSection
        "
    />
</template>
<script setup lang="ts">
import { computed, inject, onMounted, ref, watchEffect } from "vue"
import LegalSection from "@/components/sections/LegalSection.vue"
import PrivatesSection from "@/components/sections/PrivatesSection.vue"
import { useFormStore } from "@/stores"

const formStore = useFormStore()
const form = formStore.form
const formRef = inject("form-ref")

const options = [
    { label: "Юридическое лицо", value: "legal" },
    { label: "Физическое лицо", value: "private" },
]
</script>
