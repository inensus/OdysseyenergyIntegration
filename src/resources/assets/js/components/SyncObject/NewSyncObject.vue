<template>
    <div>
        <widget
                v-if="showAdd"
                :id="'new-bucket'"
                :title="'New Bucket'"
                :color="'red'"
        >
            <md-card>
                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div
                                class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                            <md-field
                                    :class="{'md-invalid': errors.has('bucket_name')}">
                                <label for="bucket_name">Name</label>
                                <md-input
                                        id="bucket_name"
                                        name="bucket_name"
                                        v-model="syncObjectService.syncObject.bucketName"
                                        v-validate="'required'"
                                />
                                <span
                                        class="md-error">{{ errors.first('bucket_name') }}</span>
                            </md-field>
                        </div>
                        <div
                                class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                            <md-field
                                    :class="{'md-invalid': errors.has('object_location')}">
                                <label for="object_location">Object Location</label>
                                <md-input
                                        id="object_location"
                                        name="object_location"
                                        v-model="syncObjectService.syncObject.objectLocation"
                                        v-validate="'required'"
                                />
                                <span
                                        class="md-error">{{ errors.first('object_location') }}</span>
                            </md-field>
                        </div>
                        <div
                                class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                            <md-field
                                    :class="{'md-invalid': errors.has('tag')}">
                                <label for="tag">Tag</label>
                                <md-select name="tag" v-model="syncObjectService.syncObject.tagId">
                                    <md-option v-for="(tag) in syncObjectTagService.list"
                                               :key="tag.id" :value="tag.id">
                                        {{tag.name}}
                                    </md-option>
                                </md-select>
                                <span class="md-error">{{ errors.first('tag') }}</span>
                            </md-field>
                        </div>

                    </div>


                </md-card-content>
                <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                <md-card-actions>
                    <md-button role="button" class="md-raised md-primary" @click="create">Save
                    </md-button>
                    <md-button role="button" class="md-raised" @click="hide">Close</md-button>
                </md-card-actions>
            </md-card>
        </widget>
    </div>
</template>

<script>
import Widget from '../Shared/Widget'
import { SyncObjectService } from '../../services/SyncObjectService'
import { SyncObjectTagService } from '../../services/SyncObjectTagService'
import { EventBus } from '../../eventbus'

export default {
    name: 'NewSyncObject',
    components: { Widget },
    data () {
        return {
            title: 'bucket',
            syncObjectService: new SyncObjectService(),
            syncObjectTagService: new SyncObjectTagService(),
            loading: false,
            showAdd: false,
        }

    },
    mounted () {
        EventBus.$on('showNewSyncObject', this.show)
        this.getTagNames()
    },
    methods: {
        async getTagNames () {
            try {
                await this.syncObjectTagService.getSyncObjectTags()

            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async create () {
            let validator = await this.$validator.validateAll()
            if (!validator) {
                return
            }
            try {

                await this.syncObjectService.createSyncObject()
                this.hide()
                this.alertNotify('success', 'Sync object has registered.')
                EventBus.$emit('syncObjectAdded')

            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        hide () {
            this.syncObjectService.resetSyncObject()
            this.showAdd = false
        },
        show () {
            this.showAdd = true
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
