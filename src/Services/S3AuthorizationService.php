<?php


namespace Inensus\OdysseyS3Integration\Services;


use Aws\S3\S3Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
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


    public function show()
    {
        try {

            $credentials = $this->authorizationModel->newQuery()->first();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }
        return $credentials;
    }

    public function connect(): S3Client
    {
        try {

            $credentials = $this->authorizationModel->newQuery()->first();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
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

    public function saveS3Credentials(array $data)
    {


        $credentials = $this->authorizationModel->newQuery()->first();
        if (!$credentials) {
            return $this->authorizationModel::query()->create([
                'region' => $data['region'],
                'key' => $data['access_key'],
                'secret' => $data['access_token'],
            ]);

        } else {
            $credentials = $this->authorizationModel::query()->find($data['id']);
            $credentials->secret = $data['access_token'];
            $credentials->region = $data['region'];
            $credentials->key = $data['access_key'];
            $credentials->update();
            $credentials->save();
            return $credentials;
        }
    }


}
