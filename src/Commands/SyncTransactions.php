<?php


namespace Inensus\OdysseyS3Integration\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Inensus\OdysseyS3Integration\Services\S3SyncService;
use Inensus\OdysseyS3Integration\Services\TransactionSynchronizeService;

class SyncTransactions extends Command
{

    protected $signature = 's3:sync-transactions {date?}';
    protected $description = 'Synchronizes the transactions wiht Odyssesy\'s S3 Buckets';

    /**
     * @var S3SyncService
     */
    private $syncService;
    /**
     * @var TransactionSynchronizeService
     */
    private $transactionSynchronizeService;


    /**
     * SyncTransactions constructor.
     * @param S3SyncService $syncService
     * @param TransactionSynchronizeService $transactionSynchronizeService
     */
    public function __construct(
        S3SyncService $syncService,
        TransactionSynchronizeService $transactionSynchronizeService
    ) {
        parent::__construct();

        $this->syncService = $syncService;
        $this->transactionSynchronizeService = $transactionSynchronizeService;
    }


    public function handle(): void
    {
        $date = $this->argument('date') ?? Carbon::now()->add('-1 days')->format('Y-m-d');

        $this->syncService->downloadFile('transactions.csv');

        $this->transactionSynchronizeService->getTransactions($date);

        $this->syncService->uploadFile('transactions.csv');

    }
}
