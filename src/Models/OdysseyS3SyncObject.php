<?php


namespace Inensus\OdysseyS3Integration\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OdysseyS3SyncObject extends Model
{
    protected $guarded = ['id'];

    public function tag(): HasOne
    {
        return $this->hasOne(OdysseyS3SyncObjectTag::class, 'id','tag_id');
    }
}
