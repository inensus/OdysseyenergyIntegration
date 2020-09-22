<?php


namespace Inensus\OdysseyS3Integration\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class S3AuthorizationModel
 * @package Inensus\OdysseyS3Integration\Models
 *
 * @property string $region
 * @property string $version
 * @property string $key
 * @property string $secret
 */
class S3AuthorizationModel extends Model
{
    protected $guarded = ['id'];

}
