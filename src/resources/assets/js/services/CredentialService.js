import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class CredentialService {

    constructor () {
        this.repository = Repository.get('credential')
        this.credential = {
            id: null,
            accessKey: null,
            accessToken: null,
            region: null
        }
    }

    fromJson (credentialData) {
        this.credential = {
            id: credentialData.id,
            accessKey: credentialData.key,
            accessToken: credentialData.secret,
            region: credentialData.region
        }
        return this.credential
    }

    async getCredential () {
        try {
            let response = await this.repository.get()
            if (response.status === 200) {
                if (Object.values(response.data.data).length > 0) {
                    this.fromJson(response.data.data)
                    return true
                } else {
                    return false
                }
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async saveCredential () {
        try {
            let credentialPM = {
                id: this.credential.id,
                access_key: this.credential.accessKey,
                access_token: this.credential.accessToken,
                region: this.credential.region
            }
            let response = await this.repository.create(credentialPM)
            if (response.status === 200 || response.status === 201) {

                return this.fromJson(response.data.data)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

}
