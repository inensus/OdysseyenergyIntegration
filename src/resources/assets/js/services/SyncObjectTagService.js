import { ErrorHandler } from '../Helpers/ErrorHander'
import Repository from '../repositories/RepositoryFactory'

export class SyncObjectTagService {
    constructor () {
        this.repository = Repository.get('syncObjectTag')
        this.list=[]
    }


    async getSyncObjectTags () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                this.list = response.data.data
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }
}
