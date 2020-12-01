<?php

namespace Inensus\OdysseyS3Integration\Services;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inensus\OdysseyS3Integration\Exceptions\S3CredentialsNotFound;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObject;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObjectTag;


class S3SyncService
{


    private $authorizationService;
    /**
     * @var OdysseyS3SyncObject
     */
    private $syncObject;
    private $odysseyS3SyncObjectTag;
    private $historyService;
    private $transactionSynchronizeService;

    public function __construct(
        S3AuthorizationService $authorizationService,
        OdysseyS3SyncObject $syncObject,
        OdysseyS3SyncObjectTag $odysseyS3SyncObjectTag,
        S3HistoryService $historyService,
        TransactionSynchronizeService $transactionSynchronizeService
    ) {
        $this->odysseyS3SyncObjectTag = $odysseyS3SyncObjectTag;
        $this->authorizationService = $authorizationService;
        $this->syncObject = $syncObject;
        $this->historyService = $historyService;
        $this->transactionSynchronizeService = $transactionSynchronizeService;
    }


    public function downloadFile($fileName, $type): void
    {
        //connect to s3
        try {
            $client = $this->authorizationService->connect();
        } catch (S3CredentialsNotFound $exception) {
            Log::critical('Autentication failed',
                ['message' => 'S3 Authentication failed with following reason :' . $exception->getMessage()]);
            return;
        }

        // get bucket from the database
        $tag = $this->odysseyS3SyncObjectTag->newQuery()->where('name', $type)->first();
        $object = $this->syncObject->newQuery()->where('tag_id', $tag->id)->first();


        $file = $client->getObject([
            'Bucket' => $object->bucket_name,
            'Key' => $object->object_location,
        ]);

        if (!File::isDirectory(storage_path('/odyssey-s3-integration'))) {
            File::makeDirectory(storage_path('/odyssey-s3-integration'), 0755, true, true);
        }

        Storage::put('odyssey-s3-integration/' . $fileName, $file['Body']);

    }

    public function uploadFile($fileName, $type, $historyId = null)
    {
        //connect to s3
        try {
            $client = $this->authorizationService->connect();
        } catch (S3CredentialsNotFound $exception) {
            Log::critical('Autentication failed',
                ['message' => 'S3 Authentication failed with following reason :' . $exception->getMessage()]);
            return;
        }

        // get bucket from the database
        $tag = $this->odysseyS3SyncObjectTag->newQuery()->where('name', $type)->first();
        $object = $this->syncObject->newQuery()->where('tag_id', $tag->id)->first();

        try {
            $client->putObject([
                'Bucket' => $object->bucket_name,
                'Key' => $object->object_location,
                'SourceFile' => storage_path('app/odyssey-s3-integration/' . $fileName)
            ]);

            if (!$historyId) {

                $this->historyService->createHistoryRecord($type, 'Successed');
            } else {

                $this->historyService->updatesSyncHistoryId($historyId, 'Successed');
            }


        } catch (\Exception $exception) {
            if (!$historyId) {
                $this->historyService->createHistoryRecord($type, 'Failed');
            } else {
                $this->historyService->updatesSyncHistoryId($historyId, 'Failed');
            }

            Log::critical('Failed to upload ' . $fileName . ' to S3 Bucket ' . $object->bucket_name,
                [
                    'message' => $exception->getMessage(),
                    'id' => '324237t8reghjk789hgjke'
                ]);
        }
    }

    public function getSyncObject($syncObject)
    {
        try {
            $syncObj = $this->syncObject->query()->with(['tag'])->findOrFail($syncObject->id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }
        return $syncObj;
    }

    public function getSyncObjects($request)
    {
        $perPage = $request->input('per_page') ?? 15;
        return $this->syncObject::with('tag')->paginate($perPage);
    }

    public function createSyncObject(array $data)
    {
        return $this->syncObject::query()->create([
            'bucket_name' => $data['bucket_name'],
            'object_location' => $data['object_location'],
            'tag_id' => $data['tag_id'],
        ]);
    }

    public function updateSyncObject($syncObject, $data)
    {

        $syncObject->bucket_name = $data['bucket_name'];
        $syncObject->object_location = $data['object_location'];
        $syncObject->tag_id = $data['tag_id'];
        $syncObject->save();
        return $syncObject->fresh();
    }

    public function deleteSyncObject($syncObject)
    {
        $syncObject->delete();
    }

    public function checkS3Connection($request)
    {

        $bucketName = $request->input('bucket');

        $client = $this->authorizationService->connect();
        $syncObjs = array();

        if ($bucketName == 'all') {
            $syncObjects = $this->syncObject->newQuery()->get();
            foreach ($syncObjects as $key => $value) {
                try {

                    $client->getObject([
                        'Bucket' => $value->bucket_name,
                        'Key' => $value->object_location,
                    ]);
                    $bucket = ['id' => $value->id, 'isAuthenticate' => true];
                } catch (\Exception $e) {

                    $bucket = ['id' => $value->id, 'isAuthenticate' => false];
                }
                array_push($syncObjs, $bucket);
            }
        } else {
            try {
                $syncObject = $this->syncObject->newQuery()->where('bucket_name', $bucketName)->firstOrFail();
                try {
                    $client->getObject([
                        'Bucket' => $syncObject->bucket_name,
                        'Key' => $syncObject->object_location,
                    ]);
                    $bucket = ['id' => $syncObject->id, 'isAuthenticate' => true];
                } catch (\Exception $e) {
                    $bucket = ['id' => $syncObject->id, 'isAuthenticate' => false];
                }
                array_push($syncObjs, $bucket);
            } catch (ModelNotFoundException $exception) {
                return ['message' => 'S3 Authentication failed with following reason :' . $exception->getMessage()];
            }
        }

        return $syncObjs;
    }

    public function resendSyncObject($data)
    {
        try {
            $history = $this->historyService->getHistoryById($data['id']);
            $fileName = strtolower($history->type) . 'csv';
            $this->downloadFile($fileName, $history->type);

            switch ($history->type) {
                case 'Transaction' :
                    $this->transactionSynchronizeService->getTransactions($history->created_at);
                    $this->uploadFile($fileName, $history->type, $history->id);
                    break;

            }
        } catch (\Exception $exception) {
            return;
        }

    }
}
