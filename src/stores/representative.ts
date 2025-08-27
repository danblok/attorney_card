import { defineStore } from "pinia"
import { useFormStore } from "@/stores/form"

export const useRepresentativeStore = defineStore("representative", () => {
    // Receive values via FormStore
    const formStore = useFormStore()

    async function loadEmployees() {}

    async function loadContacts() {}

    async function saveRepresentative() {}

    async function removeRepresentative() {}

    async function saveCompany() {}
})
