<?php


namespace Inensus\OdysseyS3Integration\Http\Controllers;


use Illuminate\Support\Facades\Log;
use Inensus\OdysseyS3Integration\Exceptions\AutorizationValidationFailedException;
use Inensus\OdysseyS3Integration\Http\Requests\S3AuthorizationCreateRequest;
use Inensus\OdysseyS3Integration\Http\Resources\S3Resource;
use Inensus\OdysseyS3Integration\Models\S3SyncModel;
use Inensus\OdysseyS3Integration\Services\S3AuthorizationService;
use Inensus\OdysseyS3Integration\Services\S3SyncService;

class S3AuthController extends Controller
{
    /**
     * @var S3SyncModel
     */
    private $s3Model;
    /**
     * @var S3SyncService
     */
    private $authorizationService;

    /**
     * S3AuthController constructor.
     * @param S3AuthorizationService $authorizationService
     */
    public function __construct(S3AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }


    public function index()
    {
        return $this->authorizationService->list();
    }

    public function store(S3AuthorizationCreateRequest $request): S3Resource
    {
        try {
            $authorization = $this->authorizationService->createAuthorization($request->all());
        } catch (AutorizationValidationFailedException $e) {
            Log::critical("Authorization creation failed", ["message" => $e->getMessage()]);
            $authorization = ['message' => $e->getMessage()];
        }
        return new S3Resource($authorization);
    }
}

