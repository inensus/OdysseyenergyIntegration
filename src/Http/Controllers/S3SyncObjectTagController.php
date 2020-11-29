<?php


namespace Inensus\OdysseyS3Integration\Http\Controllers;


use Inensus\OdysseyS3Integration\Http\Resources\S3Resource;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObjectTag;

class S3SyncObjectTagController extends Controller
{
    private $syncObjectTag;
    public function __construct(OdysseyS3SyncObjectTag $syncObjectTag)
    {
        $this->syncObjectTag=$syncObjectTag;
    }

    public function index():S3Resource
    {
        return new S3Resource($this->syncObjectTag->newQuery()->get());

    }
}
