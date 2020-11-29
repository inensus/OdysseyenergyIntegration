<?php


namespace Inensus\OdysseyS3Integration\Http\Controllers;



use Illuminate\Http\Request;
use Inensus\OdysseyS3Integration\Http\Requests\S3SyncCreateRequest;
use Inensus\OdysseyS3Integration\Http\Resources\S3Resource;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObject;
use Inensus\OdysseyS3Integration\Services\S3AuthorizationService;
use Inensus\OdysseyS3Integration\Services\S3SyncService;

class S3SyncController extends Controller
{
    /**
     * @var S3AuthorizationService
     */
    private $authorizationService;
    /**
     * @var S3SyncService
     */
    private $syncService;

    private $syncObjectModel;

    /**
     * S3SyncController constructor.
     * @param S3SyncService $syncService
     * @param OdysseyS3SyncObject $syncObjectModel
     */
    public function __construct(S3SyncService $syncService,OdysseyS3SyncObject $syncObjectModel)
    {
        $this->syncService = $syncService;
        $this->syncObjectModel =$syncObjectModel;
    }

    public function index(Request $request):S3Resource
    {
        return new S3Resource($this->syncService->getSyncObjects($request));
    }

    public function store(S3SyncCreateRequest $request): S3Resource
    {
        return new S3Resource($this->syncService->createSyncObject($request->all()));
    }

    public function update(OdysseyS3SyncObject $syncObject, S3SyncCreateRequest $request): S3Resource
    {
        return new S3Resource($this->syncService->updateSyncObject($syncObject, $request->all()));
    }

    public function show(OdysseyS3SyncObject $syncObject, Request $request):S3Resource
    {
        return new S3Resource($this->syncService->getSyncObject($syncObject));
    }

    public function destroy(OdysseyS3SyncObject $syncObject): S3Resource
    {
        return new S3Resource($this->syncService->deleteSyncObject($syncObject));
    }

    public function check(Request $request):S3Resource
    {
        return  new S3Resource($this->syncService->checkS3Connection($request));
    }
    public function resend(Request $request):S3Resource
    {


        return  new S3Resource($this->syncService->resendSyncObject($request->all()));
    }
}
