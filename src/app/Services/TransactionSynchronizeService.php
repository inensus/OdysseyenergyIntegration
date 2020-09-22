<?php


namespace Inensus\OdysseyS3Integration\Services;


use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Inensus\OdysseyS3Integration\Helpers\TransactionToCSV;

class TransactionSynchronizeService
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * TransactionSynchronizeService constructor.
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }


    public function getTransactions($date = null)
    {
        $date = $date ?? Carbon\Carbon::now()->format('Y-m-d');

        //read transactions for the given date
        $transactions = DB::select('select
            t.id,
            concat (mt.max_current, "A", mt.phase, "P") as meter_tag,
            ph.amount as payment_amount,
            cg.name as account_type,
            t.sender,
            t.message as meter_id,
            t.created_at,
            t.updated_at,
            t.original_transaction_type as payment_source,
            ph.payment_type,
            concat(ph.paid_for_type,"_", ph.paid_for_id) as reference_id,
            grids.name as utility_id
            from transactions t


            left join meters m on t.message = m.serial_number
            left join meter_types mt on m.meter_type_id= mt.id
            left join meter_parameters mp on m.id = mp.meter_id
            left join connection_groups cg on mp.connection_group_id = cg.id
            left join payment_histories ph on t.id = ph.transaction_id
            left join vodacom_transactions vt on t.original_transaction_id = vt.id and t.original_transaction_type ="vodacom_transaction"
            left join airtel_transactions at on t.original_transaction_id = at.id and t.original_transaction_type ="airtel_transaction"
            left join addresses adr on mp.id = adr.`owner_id`  and adr.owner_type="meter_parameter"
            left join cities cit on adr.`city_id` = cit.id
            left join mini_grids grids on cit.mini_grid_id = grids.id

            where (vt.status = 1 or at.status=1)  and ph.amount > 0
            and DATE(t.created_at) = :date
            order by t.id asc',
            ['date' => $date]);


        $f = fopen(storage_path('app/odyssey-s3-integration/transactions.csv'), 'a');

        //append transactions to the transaction file
        foreach ($transactions as $transaction) {
            $t = new TransactionToCSV();
            try {
                fputcsv($f, $t->toCSV($transaction));
            } catch (Exception $x) {
                echo $x->getMessage() . "\n";
            }


        }
    }
}
