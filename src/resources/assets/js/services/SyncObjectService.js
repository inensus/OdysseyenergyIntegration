import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class SyncObjectService {
    constructor () {
        this.repository = Repository.get('syncObject')
        this.syncObject = {
            id: null,
            bucketName: null,
            objectLocation: null,
            tagId: null,
            tagName: null
        }
        this.list = []

    }

    fromJson (syncObjectData) {

        return {
            id: syncObjectData.id,
            bucketName: syncObjectData.bucket_name,
            objectLocation: syncObjectData.object_location,
            tagName: syncObjectData.tag.name,
            tagId: syncObjectData.tag_id,
            authenticate: null
        }
    }

    updateList (data) {
        this.list = []
        data.forEach((e) => {
            let syncObj = this.fromJson(e)
            this.list.push(syncObj)
        })

        return this.list
    }

    async getSyncObject (syncObjectId) {
        try {
            let response = await this.repository.get(syncObjectId)
            if (response.status === 200) {
                this.syncObject = this.fromJson(response.data.data)
                return this.syncObject
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async syncObjectList () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                return this.updateList(response.data.data)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async updateSyncObject (syncObject) {
        try {
            let syncObjectPM = {
                id: syncObject.id,
                bucket_name: syncObject.bucketName,
                object_location: syncObject.objectLocation,
                tag_id: syncObject.tagId
            }

            let response = await this.repository.update(syncObjectPM)
            if (response.status === 200 || response.status === 201) {

                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async createSyncObject () {
        try {
            let syncObjectPM = {
                id: this.syncObject.id,
                bucket_name: this.syncObject.bucketName,
                object_location: this.syncObject.objectLocation,
                tag_id: this.syncObject.tagId
            }
            let response = await this.repository.create(syncObjectPM)
            if (response.status === 200 || response.status === 201) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async deleteSyncObject (syncObjectId) {
        try {
            let response = await this.repository.delete(syncObjectId)
            if (response.status === 200 || response.status === 201) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async checkSyncObjectAuthentication (param) {
        try {
            let response = await this.repository.check(param)
            if (response.status === 200 || response.status === 201) {
                let authenticates = response.data.data
                authenticates.forEach((e)=>{
                    let syncObj = this.list.filter(x => x.id === e.id)[0]
                    syncObj.authenticate = e.isAuthenticate

                })
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async resendSyncObject (syncHistory) {
        try {

            let response = await this.repository.resend(syncHistory)
            if (response.status === 200 || response.status === 201) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    resetSyncObject () {
        this.syncObject = {
            id: null,
            bucketName: null,
            objectLocation: null,
            tagId: null,
            tagName: null
        }
    }
}
