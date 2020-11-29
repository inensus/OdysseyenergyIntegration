<template>
    <div>
        <new-sync-object/>
        <widget
                :title="'Bucket'"
                :subscriber="subscriber"
                :button="true"
                button-text="New Bucket"
                @widgetAction="addNew()"
                color="green"
        >

            <md-table>
                <md-table-row>
                    <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                </md-table-row>
                <md-table-row v-for="(sync) in syncObjectService.list" style="cursor:pointer;" :key="sync.id">

                    <md-table-cell> {{ sync.id}}
                    </md-table-cell>
                    <md-table-cell>
                        <md-icon style="color:green" v-if="sync.authenticate">check_circle_outline</md-icon>
                        <md-icon style="color:red" v-if="!sync.authenticate">remove_circle_outline</md-icon>
                    </md-table-cell>
                    <md-table-cell>
                        <div v-if="editSyncObject === sync.id">
                            <md-field
                                    :class="{'md-invalid': errors.has('bucket_name')}">
                                <label for="bucket_name">Bucket Name</label>
                                <md-input
                                        id="bucket_name"
                                        name="bucket_name"
                                        v-model="sync.bucketName"
                                        v-validate="'required'"
                                />
                                <span
                                        class="md-error">{{ errors.first('bucket_name') }}</span>
                            </md-field>
                        </div>
                        <div v-else>
                            {{ sync.bucketName}}
                        </div>

                    </md-table-cell>
                    <md-table-cell>
                        <div v-if="editSyncObject === sync.id">
                            <md-field
                                    :class="{'md-invalid': errors.has('object_location')}">
                                <label for="object_location">Location</label>
                                <md-input
                                        id="object_location"
                                        name="object_location"
                                        v-model="sync.objectLocation"
                                        v-validate="'required'"
                                />
                                <span
                                        class="md-error">{{ errors.first('object_location') }}</span>
                            </md-field>
                        </div>
                        <div v-else>
                            {{ sync.objectLocation}}
                        </div>

                    </md-table-cell>
                    <md-table-cell>
                        <div v-if="editSyncObject === sync.id">

                            <md-field
                                    :class="{'md-invalid': errors.has('tag')}">
                                <label for="tag">Tag</label>
                                <md-select name="tag" v-model="sync.tagId">
                                    <md-option v-for="(tag) in syncObjectTagService.list"
                                               :key="tag.id" :value="tag.id">
                                        {{tag.name}}
                                    </md-option>
                                </md-select>
                                <span class="md-error">{{ errors.first('tag') }}</span>
                            </md-field>
                        </div>
                        <div v-else>
                            {{ sync.tagName}}
                        </div>
                    </md-table-cell>

                    <md-table-cell>
                        <div v-if="editSyncObject === sync.id">
                            <md-button class="md-icon-button" @click="updateSyncObject(sync)">
                                <md-icon>save</md-icon>
                            </md-button>
                            <md-button class="md-icon-button" @click="editSyncObject = null">
                                <md-icon>close</md-icon>
                            </md-button>
                        </div>
                        <div v-else>
                            <md-button class="md-icon-button" @click="editSyncObject = sync.id">
                                <md-icon>edit</md-icon>
                            </md-button>
                            <md-button class="md-icon-button" @click="deleteSyncObject(sync.id)">
                                <md-icon>close</md-icon>
                            </md-button>
                        </div>
                    </md-table-cell>
                </md-table-row>
            </md-table>
        </widget>
    </div>
</template>

<script>


import { SyncObjectService } from '../../services/SyncObjectService'
import Widget from '../Shared/Widget'
import { SyncObjectTagService } from '../../services/SyncObjectTagService'
import { EventBus } from '../../eventbus'
import NewSyncObject from './NewSyncObject'

export default {
    name: 'SyncObjectList',
    components: { NewSyncObject, Widget },
    data () {
        return {
            subscriber: 'syncObject-list',
            addNewAgent: false,
            syncObjectService: new SyncObjectService(),
            syncObjectTagService: new SyncObjectTagService(),
            searchTerm: '',
            headers: ['ID', 'Authenticated', 'Bucket Name', 'Location', 'Tag', '#'],
            editSyncObject: null
        }
    },

    mounted () {
        this.getSyncObjects()
        this.getTagNames()
        EventBus.$on('syncObjectAdded', this.getSyncObjects)
        EventBus.$on('syncObjectCheckAuthentication', this.checkS3Authentication)
    },
    methods: {
        async getTagNames () {
            try {
                await this.syncObjectTagService.getSyncObjectTags()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getSyncObjects () {
            try {
                this.syncObjectService.resetSyncObject()
                await this.syncObjectService.syncObjectList()
                await this.checkS3Authentication()
                EventBus.$emit('widgetContentLoaded', this.subscriber, this.syncObjectService.list.length)
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },

        async checkS3Authentication (bucketName = null) {
            if (this.syncObjectService.list.length) {
                let param = {}
                param['bucket'] = 'all'
                if (bucketName) {
                    param['bucket'] = bucketName
                }
                try {
                    await this.syncObjectService.checkSyncObjectAuthentication(param)
                } catch (e) {
                    this.alertNotify('error', e)
                }
            }
        },

        async updateSyncObject (syncObject) {
            try {
                let validator = await this.$validator.validateAll()
                if (!validator) {

                    return
                }
                try {
                    let bucket = await this.syncObjectService.updateSyncObject(syncObject)
                    await this.checkS3Authentication(bucket.bucket_name)
                    this.editSyncObject = null
                    this.alertNotify('success', 'Bucket updated successfully')

                } catch (e) {
                    this.alertNotify('error', e)
                }

            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async deleteSyncObject (syncObjectId) {
            try {
                await this.syncObjectService.deleteSyncObject(syncObjectId)
                await this.getSyncObjects()
                this.alertNotify('success', 'Bucket deleted successfully')
            } catch (e) {
                this.alertNotify('error', e)
            }
        },
        addNew () {
            EventBus.$emit('showNewSyncObject')
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
