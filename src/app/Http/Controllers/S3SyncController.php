<?php


namespace Inensus\OdysseyS3Integration\Http\Controllers;


use Inensus\OdysseyS3Integration\Http\Requests\S3SyncCreateRequest;
use Inensus\OdysseyS3Integration\Http\Resources\S3Resource;
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

    /**
     * S3SyncController constructor.
     * @param S3SyncService $syncService
     */
    public function __construct(S3SyncService $syncService)
    {
        $this->syncService = $syncService;
    }


    public function store(S3SyncCreateRequest $request): S3Resource
    {
        return new S3Resource($this->syncService->create($request->all()));
    }
}
