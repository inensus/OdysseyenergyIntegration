export class SyncHistoryService {
    constructor () {
        this.list=[]
        this.pagingUrl='/api/odyssey-s3/sync-history'
        this.routeName='/odyssey-s3/s3-Overview'
    }

    fromJson (historyData) {
        this.list=[]
        for (let h in historyData) {
            let history={
                id :historyData[h].id,
                syncObjectId:historyData[h].sync_object_id,
                status:historyData[h].status,
                type:historyData[h].type,
                date:historyData[h].created_at
            }
            this.list.push(history)
        }
    }
    updateList (data) {
        this.list = []
        return this.fromJson(data)
    }
}
