<template>
    <div>
        <widget :id="title"
                :title="title"
                :paginator="false">
            <form @submit.prevent="submitCredentialForm" data-vv-scope="Credential-Form" class="Credential-Form">
                <md-card>
                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div
                                    class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                                <md-field
                                        :class="{'md-invalid': errors.has('Credential-Form.region')}">
                                    <label for="region">Region</label>
                                    <md-input
                                            id="region"
                                            name="region"
                                            v-model="credentialService.credential.region"
                                            v-validate="'required'"
                                    />
                                    <span
                                            class="md-error">{{ errors.first('Credential-Form.region') }}</span>
                                </md-field>
                            </div>
                            <div
                                    class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                                <md-field
                                        :class="{'md-invalid': errors.has('Credential-Form.access_key')}">
                                    <label for="access_key">Access Key</label>
                                    <md-input
                                            id="access_key"
                                            name="access_key"
                                            v-model="credentialService.credential.accessKey"
                                            v-validate="'required'"
                                    />
                                    <span
                                            class="md-error">{{ errors.first('Credential-Form.access_key') }}</span>
                                </md-field>
                            </div>
                            <div
                                    class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                                <md-field
                                        :class="{'md-invalid': errors.has('Credential-Form.authentication_token')}">
                                    <label for="access_token">Authentication Token</label>
                                    <md-input
                                            id="access_token"
                                            name="access_token"
                                            v-model="credentialService.credential.accessToken"
                                            v-validate="'required'"
                                    />
                                    <span class="md-error">{{ errors.first('Credential-Form.access_token') }}</span>
                                </md-field>
                            </div>

                        </div>
                    </md-card-content>
                    <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                    <md-card-actions>
                        <md-button class="md-raised md-primary" type="submit">Save</md-button>
                    </md-card-actions>
                </md-card>

            </form>
        </widget>

    </div>
</template>

<script>
import { CredentialService } from '../../services/CredentialService'
import Widget from '../Shared/Widget'
import { EventBus } from '../../eventbus'

export default {
    name: 'Credential',
    components: { Widget },
    data () {
        return {
            title: 'Credential',
            credentialService: new CredentialService(),
            loading: false,
            authorized: false,
        }
    },
    mounted () {
        this.getCredential()
    },
    methods: {
        async getCredential () {
            try {
                await this.credentialService.getCredential()
            } catch (e) {

                this.alertNotify('error', e.message)
            }
        },
        async submitCredentialForm () {
            let validator = await this.$validator.validateAll('Credential-Form')
            if (validator) {
                try {
                    this.loading = true
                    await this.credentialService.saveCredential()
                    this.loading = false
                    EventBus.$emit('syncObjectCheckAuthentication')
                    this.alertNotify('success', 'Credentials saved successfully.')
                } catch (e) {
                    this.loading = false
                    this.alertNotify('error', e.message)
                }
            }
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
