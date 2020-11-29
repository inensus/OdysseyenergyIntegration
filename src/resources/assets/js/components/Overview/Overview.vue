<template>
    <div>
        <div class="overview-line">
            <div class="md-layout md-gutter">

                <div class="md-layout-item md-small-size-50  md-xsmall-size-50 md-medium-size-50 md-size-50">
                    <credential/>
                </div>
                <div class="md-layout-item md-small-size-50  md-xsmall-size-50 md-medium-size-50 md-size-50">

                    <sync-history style="height: 100%!important;"/>
                </div>


            </div>

        </div>
        <div class="overview-line">
            <div class="md-layout md-gutter">


                <div class="md-layout-item md-small-size-100  md-xsmall-size-100 md-medium-size-100  md-size-100">
                    <sync-object-list/>

                </div>
            </div>

        </div>


    </div>
</template>

<script>
import Credential from '../Credential/Credential'
import SyncHistory from '../SyncHistory/SyncHistory'
import SyncObjectList from '../SyncObject/SyncObjectList'

export default {
    name: 'Overview',
    components: { Credential, SyncHistory, SyncObjectList }
}
</script>

<style scoped>
    .overview-line {
        margin-top: 1rem;
    }
</style>
