const resource =  '/api/odyssey-s3/credentials'
import Client from './Client/AxiosClient'

export default {
    create(credentials){
        return  Client.post(`${resource}`,credentials)
    },
    get(){
        return  Client.get(`${resource}`)
    }

}
