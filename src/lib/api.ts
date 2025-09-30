type HTTPMethod = "GET" | "POST" | "PUT" | "PATCH" | "DELETE"

type ResultError = {
    error: string
}

type ResultSuccess<T> = {
    data: T
}

const base = "/ac/api"

export async function api<T>(method: HTTPMethod, resource: string) {
    try {
        const res = await fetch(base + resource, {
            method: method,
            headers: {
                "Content-Type": "application/json",
            },
        })

        const data: ResultSuccess<T> | ResultError = await res.json()
        return data
    } catch (error) {
        if (error instanceof Error) {
            return { error: error.message }
        }

        return { error: "Неизвестная ошибка при вызове API" }
    }
}
