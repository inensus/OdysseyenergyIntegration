<?php


namespace Inensus\OdysseyS3Integration\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdysseyS3SyncHistory extends Model
{
    protected $guarded = ['id'];

    public function syncObjects():belongsTo
    {
        return $this->belongsTo(OdysseyS3SyncObject::class, 'sync_object_id', 'id');
    }
}
