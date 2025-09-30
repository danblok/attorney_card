import type { UploadUserFile } from "element-plus"

export interface FormModel {
    id: string
    subjectId: string
    privateRepresentatives: PrivateRepresentative[]
    legalRepresentative: LegalRepresentative
    attorneyData: AttorneyData
    authorities: Authorities
}

export interface Project extends FormModel {
    status: string
    isCancelled: boolean
    isReadonly: boolean
}

export interface PrivateRepresentative {
    id: string
    firstName: string
    lastName: string
    secondName: string
    position: string
    externalId: string
    isGardiaEmployee: boolean
    passportIssuedBy: string
    passportIssuedDate: string
    divisionCode: string
    registrationAddress: string
    additionalInfo: string
    passportSeries: string
    passportNumber: string
}

export interface LegalRepresentative {
    company: Company
    representative: Contact
    basePowerDocument: string
}

export interface AttorneyData {
    internalNumber: string
    externalNumber: string
    status: string
    department: string
    initiator: User
    executiveAuthority: User
    issueDate: string
    startDate: string
    endDate: string
    isNotarialPowerOfAttorney: boolean
    issueForm: string
    template: string
    file: string
    copiesCount: number
    system: string
    justification: string
    activityArea: string
    additionalInfo: string
}

export interface Authorities {
    values: string[]
    freeForm: string
    isAuthoritiesInFreeForm: boolean
}

type BaseEntity = {
    id: string
    value: string
}

export type Template = BaseEntity
export type Form = BaseEntity
export type Subject = BaseEntity
export type SystemEntity = BaseEntity
export type ActivityArea = BaseEntity
export type Authority = BaseEntity
export type Status = BaseEntity

export type Company = {
    id: string
    fullName: string
    shortName: string
    ogrn: string
    inn: string
    address: string
}

export type Contact = {
    id: string
    firstName: string
    lastName: string
    secondName: string
    position: string
}

export interface User {
    id: string
    firstName: string
    lastName: string
    secondName: string
    position: string
}

export type LoadingStatus = "loading" | "finished"
