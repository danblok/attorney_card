<script setup lang="ts">
import LegalSection from "@/components/sections/LegalSection.vue"
import PrivatesSection from "@/components/sections/PrivatesSection.vue"
import { useStore } from "@/stores"

const store = useStore()
const form = store.project
</script>

<template>
    <el-form-item>
        <el-form-item label="Представитель по доверенности" required>
            <el-select v-model="form.subjectId" :options="store.subjects">
                <el-option
                    v-for="item in store.subjects"
                    :key="item.id"
                    :label="item.value"
                    :value="item.id"
                />
            </el-select>
        </el-form-item>
    </el-form-item>
    <component :is="form.subjectId === '60' ? PrivatesSection : LegalSection" />
</template>
