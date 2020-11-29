import CredentialRepository from './CredentialRepository'
import SyncObjectRepository from './SyncObjectRepository'
import SyncObjectTagRepository from './SyncObjectTagRepository'
import PaginatorRepository from './PaginatorRepository'


const repositories = {
    'credential':CredentialRepository,
    'syncObject':SyncObjectRepository,
    'syncObjectTag':SyncObjectTagRepository,
    'paginate':PaginatorRepository

}
export default {
    get: name => repositories[name]
}
