<template>
    <div>
        <widget
                :title="'History'"
                id="sync-history"
                :subscriber="subscriber"
                :paginator="true"
                :resetKey="resetKey"
                :paging_url="syncHistoryService.pagingUrl"
                :route_name="syncHistoryService.routeName"
                :show_per_page="true"
                color="green"
        >
            <div slot="tabbar">
                <md-field>
                    <label for="tag">Tag</label>
                    <md-select name="tag" v-model="selectedTag" @md-selected="selectTag">
                        <md-option v-for="(tag,i) in syncObjectTagService.list"
                                   :key="i" :value="tag.id">
                            {{tag.name}}
                        </md-option>
                    </md-select>
                </md-field>
            </div>

            <md-table>
                <md-table-row>
                    <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                </md-table-row>
                <md-table-row v-for="(history) in syncHistoryService.list" style="cursor:pointer;" :key="history.id">

                    <md-table-cell> {{ history.id}}</md-table-cell>
                    <md-table-cell> {{ history.type}}</md-table-cell>
                    <md-table-cell> {{ history.status}}</md-table-cell>
                    <md-table-cell> {{ history.date}}</md-table-cell>
                    <md-table-cell>
                        <md-button class="md-icon-button" @click="resend(history)"
                                   :disabled="history.status==='Successed'">
                            <md-icon>cached</md-icon>
                        </md-button>
                        <md-tooltip md-direction="top">Retry</md-tooltip>
                    </md-table-cell>
                </md-table-row>
            </md-table>

            <md-progress-bar md-mode="indeterminate" v-if="loading"/>


        </widget>
    </div>
</template>

<script>
import Widget from '../Shared/Widget'
import { EventBus } from '../../eventbus'
import { SyncHistoryService } from '../../services/SyncHistoryService'
import { SyncObjectTagService } from '../../services/SyncObjectTagService'
import { SyncObjectService } from '../../services/SyncObjectService'

export default {
    name: 'SyncHistory',
    components: { Widget },
    data () {
        return {
            syncHistoryService: new SyncHistoryService(),
            syncObjectTagService: new SyncObjectTagService(),
            syncObjectService: new SyncObjectService(),
            subscriber: 'sync-history',
            loading: false,
            title: 'Sync History',
            selectedTag: '',
            headers: ['ID', 'Type', 'Status', 'Date', '#'],
            resetKey: 0,
        }
    },
    mounted () {
        this.getTagNames()
        EventBus.$on('pageLoaded', this.reloadList)
    },
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
    },
    methods: {
        async getTagNames () {
            try {
                let tags = await this.syncObjectTagService.getSyncObjectTags()
                this.selectedTag = tags[0].id
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async resend (history) {
            try {
                this.loading = true
                await this.syncObjectService.resendSyncObject(history)
                this.loading = false
                this.resetKey += 1
                EventBus.$emit('widgetContentLoaded', this.subscriber, this.syncHistoryService.list.length)
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        selectTag (tag) {
            let tagName = this.syncObjectTagService.list.filter(x => x.id = tag).map(x => {return x.name})[0]
            if (tagName) {
                EventBus.$emit('loadPage', null, { 'term': tagName })
                EventBus.$emit('widgetContentLoaded', this.subscriber, this.syncHistoryService.list.length)
            }
        },
        reloadList (subscriber, data) {

            if (subscriber !== this.subscriber) return
            this.syncHistoryService.updateList(data)
            EventBus.$emit('widgetContentLoaded', this.subscriber, this.syncHistoryService.list.length)
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    }

}
</script>

<style scoped>

</style>
