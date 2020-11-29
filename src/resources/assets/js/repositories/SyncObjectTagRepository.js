const resource =  '/api/odyssey-s3/sync-object-tag'
import Client from './Client/AxiosClient'

export default {

    list(){
        return Client.get(`${resource}`)
    }
}
