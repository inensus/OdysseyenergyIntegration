<?php


namespace Inensus\OdysseyS3Integration\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OdysseyS3SyncObjectTag extends Model
{
    protected $guarded = ['id'];

    public function syncObject():belongsTo
    {
        return $this->belongsTo(OdysseyS3SyncObject::class, 'id', 'tag_id');
    }
}
