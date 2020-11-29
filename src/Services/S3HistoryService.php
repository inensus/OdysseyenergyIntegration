<?php


namespace Inensus\OdysseyS3Integration\Services;


use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncHistory;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObject;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObjectTag;

class S3HistoryService
{

    private $odysseyS3SyncHistory;
    private $odysseyS3SyncObjectTag;
    private $odysseyS3SyncObject;

    public function __construct(
        OdysseyS3SyncHistory $odysseyS3SyncHistory,
        OdysseyS3SyncObjectTag $odysseyS3SyncObjectTag,
        OdysseyS3SyncObject $odysseyS3SyncObject
    ) {
        $this->odysseyS3SyncHistory = $odysseyS3SyncHistory;
        $this->odysseyS3SyncObjectTag = $odysseyS3SyncObjectTag;
        $this->odysseyS3SyncObject = $odysseyS3SyncObject;
    }

    public function getHistoryByTag($request)
    {
        $tagName = $request->input('term')??'Transaction';
        $perPage = $request->input('per_page') ?? 15;
        return $this->odysseyS3SyncHistory->newQuery()->with('syncObjects.tag')->whereHas('syncObjects.tag',
            function ($q) use ($tagName) {
                $q->where('name', $tagName);
            })->paginate($perPage);
    }

    public function createHistoryRecord($tagName,$status)
    {
        $tag = $this->odysseyS3SyncObjectTag->newQuery()->where('name', $tagName)->first();
        $syncObject =$this->odysseyS3SyncObject->newQuery()->where('tag_id', $tag->id)->first();
       return   $this->odysseyS3SyncHistory->create([
                'sync_object_id'=>$syncObject->id,
                'type'=>$tagName,
                'status'=>$status
            ]);
    }

    public function getHistoryById($historyId)
    {

        return $this->odysseyS3SyncHistory->newQuery()->with('syncObjects.tag')->where('id',$historyId)->first();
    }

    public function updatesSyncHistoryId($historyId,$status)
    {
        return  $this->odysseyS3SyncHistory->newQuery()->find($historyId)->update([
          'status'=>$status
      ]);

    }

}

