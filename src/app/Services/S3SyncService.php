<?php

namespace Inensus\OdysseyS3Integration\Services;

use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObject;
use Inensus\OdysseyS3Integration\Models\S3SyncModel;

class S3SyncService
{


    /**
     * @var S3SyncModel
     */
    private $syncModel;
    /**
     * @var S3AuthorizationService
     */
    private $authorizationService;
    /**
     * @var OdysseyS3SyncObject
     */
    private $syncObject;

    public function __construct(S3AuthorizationService $authorizationService, OdysseyS3SyncObject $syncObject)
    {

        $this->authorizationService = $authorizationService;
        $this->syncObject = $syncObject;
    }

    public function create($data)
    {
        return $this->syncModel->newQuery()->create($data);
    }


    public function downloadFile($fileName): void
    {
        //connect to s3
        try {
            $client = $this->authorizationService->connect();
        } catch (S3CredentialsNotFound $exception) {
            Log::critical('Autentication failed',
                ['message' => 'S3 Authentication failed with following reason :' . $exception->getMessage()]);
            return;
        }
        // get transaction bucket from the database
        $object = $this->syncObject->first();

        $file = $client->getObject([
            'Bucket' => $object->bucket_name,
            'Key' => $object->object_location,
        ]);

        if (!File::isDirectory(storage_path('/odyssey-s3-integration'))) {
            File::makeDirectory(storage_path('/odyssey-s3-integration'), 0755, true, true);
        }

        Storage::put('odyssey-s3-integration/' . $fileName, $file['Body']);

    }

    public function uploadFile($fileName)
    {
        //connect to s3
        try {
            $client = $this->authorizationService->connect();
        } catch (S3CredentialsNotFound $exception) {
            Log::critical('Autentication failed',
                ['message' => 'S3 Authentication failed with following reason :' . $exception->getMessage()]);
            return;
        }

        // get transaction bucket from the database
        $object = $this->syncObject->first();

        try {
            $client->putObject([
                'Bucket' => $object->bucket_name,
                'Key' => $object->object_location,
                'SourceFile' => storage_path('app/odyssey-s3-integration/' . $fileName)
            ]);
        } catch (S3Exception $exception) {
            Log::critical('Failed to upload ' . $fileName . ' to S3 Bucket ' . $object->bucket_name,
                [
                    'message' => $exception->getMessage(),
                    'id' => '324237t8reghjk789hgjke'
                ]);
        }
    }
}
