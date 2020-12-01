<?php

namespace Inensus\OdysseyS3Integration\Helpers;

use stdClass;

class TransactionToCSV
{
    public $id;
    public $meterTags;
    public $paymentAmount;
    public $accountType;
    public $from;
    public $meterId;
    public $referenceId;
    public $lastSync;
    public $lastUpdate;
    public $timestamp;
    public $paymentSource;
    public $status;
    public $origin;
    public $externalId;
    public $memo;
    public $error;
    public $utilityId;


    public function __construct()
    {


    }

    public function toCSV($transaction): array
    {
        $this->fillTransactionData($transaction);
        $variables = get_object_vars($this);
        $variables = array_map([$this, 'fillWithDefaultValues'], $variables);
        return array_values($variables);
    }


    private function fillWithDefaultValues($val)
    {
        return $val ?? " ";
    }

    private function fillTransactionData($transaction): void
    {
        $this->status = 'processed';

        if ($transaction instanceof stdClass) {
            $this->id = $transaction->id;
            $this->meterTags = $transaction->meter_tag;
            $this->paymentAmount = $transaction->payment_amount;
            $this->accountType = $transaction->account_type;
            $this->from = $transaction->sender;
            $this->meterId = $transaction->meter_id;
            $this->timestamp = $transaction->created_at;
            $this->lastUpdate = $transaction->updated_at;
            $this->paymentSource = $transaction->payment_source;
            $this->origin = $transaction->payment_type;
            $this->utilityId = $transaction->utility_id;
            $this->referenceId = $transaction->reference_id;
        } elseif (is_array($transaction)) {
            $this->id = $transaction['id'];
            $this->meterTags = $transaction['meter_tag'];
            $this->paymentAmount = $transaction['payment_amount'];
            $this->accountType = $transaction['account_type'];
            $this->from = $transaction['sender'];
            $this->meterId = $transaction['meter_id'];
            $this->timestamp = $transaction['created_at'];
            $this->lastUpdate = $transaction['updated_at'];
            $this->paymentSource = $transaction['payment_source'];
            $this->origin = $transaction['payment_type'];
            $this->referenceId = $transaction['reference_id'];
            $this->utilityId = $transaction['utility_id'];
        }
    }
}
