import { ref } from "vue"
import { defineStore } from "pinia"
import type {
    Company,
    Contact,
    PrivateRepresentative,
    Subject,
    Form,
    Template,
    SystemEntity,
    ActivityArea,
    Authority,
    User,
    Status,
    Project,
    LoadingStatus,
} from "@/types"
import { api } from "@/lib/api"
import { Helper } from "@/lib/helper"
import type { UploadUserFile } from "element-plus"

export const useStore = defineStore("global", () => {
    // Данные для выбора
    const representatives = ref<PrivateRepresentative[]>([])
    const systems = ref<SystemEntity[]>([])
    const templates = ref<Template[]>([])
    const forms = ref<Form[]>([])
    const subjects = ref<Subject[]>([])
    const companies = ref<Company[]>([])
    const statuses = ref<Status[]>([])
    const contacts = ref<Contact[]>([])
    const currentUser = ref<User>(Helper.createPrivateRepresentative())
    const activityAreas = ref<ActivityArea[]>([])
    const authorities = ref<Authority[]>([])
    const director = ref<User>(Helper.createPrivateRepresentative())
    const users = ref<User[]>([])
    const file = ref<UploadUserFile>()

    // Форма и сам объект доверенности
    const project = ref<Project>(Helper.createProject())

    const loadingStatus = ref<LoadingStatus>("loading")

    async function fetchRepresentatives() {
        const res = await api<PrivateRepresentative[]>(
            "GET",
            "/representatives",
        )
        if ("error" in res) {
            console.error(res.error)
            return
        }

        representatives.value = res.data
        console.log("representatives:", res.data)
    }

    async function fetchCurrentUser() {
        const res = await api<PrivateRepresentative>("GET", "/users/current")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        currentUser.value = res.data
        console.log("current user:", res.data)
    }

    async function fetchSystems() {
        const res = await api<SystemEntity[]>("GET", "/systems")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        systems.value = res.data
        console.log("systems:", res.data)
    }

    async function fetchTemplates() {
        const res = await api<Template[]>("GET", "/templates")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        templates.value = res.data
        console.log("templates:", res.data)
    }

    async function fetchForms() {
        const res = await api<Form[]>("GET", "/forms")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        forms.value = res.data
        console.log("forms:", res.data)
    }

    async function fetchSubjects() {
        const res = await api<Subject[]>("GET", "/subjects")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        subjects.value = res.data
        console.log("subjects:", res.data)
    }

    async function fetchContacts() {
        const res = await api<Contact[]>("GET", "/contacts")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        contacts.value = res.data
        console.log("contacts:", res.data)
    }

    async function fetchCompanies() {
        const res = await api<Company[]>("GET", "/companies")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        companies.value = res.data
        console.log("companies:", res.data)
    }

    async function fetchAuthorities() {
        const res = await api<Authority[]>("GET", "/authorities")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        authorities.value = res.data
        console.log("authorities:", res.data)
    }

    async function fetchActivityAreas() {
        const res = await api<ActivityArea[]>("GET", "/areas")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        activityAreas.value = res.data
        console.log("activity areas:", res.data)
    }

    async function fetchDirector() {
        const res = await api<PrivateRepresentative>("GET", "/users/director")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        director.value = res.data
        console.log("director:", res.data)
    }

    async function fetchUsers() {
        const res = await api<PrivateRepresentative[]>("GET", "/users/list")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        users.value = res.data
        console.log("users:", res.data)
    }

    async function fetchFileInfo(id: string) {
        const query = new URLSearchParams({ id })
        const res = await api<UploadUserFile>(
            "GET",
            "/file/info?" + query.toString(),
        )
        if ("error" in res) {
            console.error(res.error)
            return
        }

        file.value = res.data
        file.value.status = "ready"
        console.log("file:", file.value)
    }

    async function fetchProject(id: string) {
        const query = new URLSearchParams({ id })
        const res = await api<Project>(
            "GET",
            "/project/info?" + query.toString(),
        )
        if ("error" in res) {
            console.error(res.error)
            return
        }

        project.value = res.data
        console.log("project:", project.value)
    }

    async function fetchStatuses() {
        const res = await api<Status[]>("GET", "/statuses")
        if ("error" in res) {
            console.error(res.error)
            return
        }

        statuses.value = res.data
        console.log("statuses:", res.data)
    }

    async function save() {
        console.log("PROJECT SAVED:", project.value)
    }

    return {
        representatives,
        systems,
        templates,
        forms,
        subjects,
        statuses,
        companies,
        contacts,
        currentUser,
        authorities,
        activityAreas,
        director,
        users,
        file,
        project,
        loadingStatus,

        fetchRepresentatives,
        fetchCurrentUser,
        fetchCompanies,
        fetchContacts,
        fetchForms,
        fetchSubjects,
        fetchSystems,
        fetchTemplates,
        fetchStatuses,
        fetchAuthorities,
        fetchActivityAreas,
        fetchDirector,
        fetchUsers,
        fetchFileInfo,
        fetchProject,
        save,
    }
})
