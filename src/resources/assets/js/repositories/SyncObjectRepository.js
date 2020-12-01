const resource =  '/api/odyssey-s3/sync-object'
import Client from './Client/AxiosClient'

export default {
    create(syncObject){
        return  Client.post(`${resource}`,syncObject)
    },
    update(syncObject){
        return  Client.put(`${resource}/${syncObject.id}`,syncObject)
    },
    delete(syncObjectId){
        return  Client.delete(`${resource}/${syncObjectId}`)
    },
    get(syncObjectId){
        return  Client.get(`${resource}/${syncObjectId}`)
    },
    list(){
        return  Client.get(`${resource}`)
    },
    check(params){

        return  Client.get(`${resource}/check/sync`,{params:params})
    },
    resend(syncHistory){
        return  Client.post(`${resource}/resend`,syncHistory)
    }
}
