<?php


namespace Inensus\OdysseyS3Integration\Services;


use Aws\S3\S3Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Inensus\OdysseyS3Integration\Exceptions\AutorizationValidationFailedException;
use Inensus\OdysseyS3Integration\Exceptions\S3CredentialsNotFound;
use Inensus\OdysseyS3Integration\Models\OdysseyS3Credential;

class S3AuthorizationService
{

    /**
     * @var OdysseyS3Credential
     */
    private $authorizationModel;

    public function __construct(OdysseyS3Credential $authorizationModel)
    {
        $this->authorizationModel = $authorizationModel;
    }

    public function list()
    {
        return $this->authorizationModel->newQuery()->get();
    }

    public function createAuthorization(array $data)
    {
        if (!array_key_exists('authorization_token') || !array_key_exists('authorization_key')) {
            throw new AutorizationValidationFailedException('required fields not present');
        }
        return $this->authorizationModel::query()->create($data);
    }

    public function connect(): S3Client
    {
        try {
            $credentials = $this->authorizationModel->newQuery()->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new S3CredentialsNotFound($e->getMessage());
        }

        return new S3Client([
            'region' => $credentials->region,
            'version' => $credentials->version,
            'credentials' => [
                'key' => $credentials->key,
                'secret' => $credentials->secret,
            ],
        ]);
    }
}
