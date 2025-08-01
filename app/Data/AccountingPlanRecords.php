<?php

namespace App\Data;

class AccountingPlanRecords
{
    /**
     * Create a new class instance.
     */
    protected $id;
    protected $accountingPlanRecords = [];
    public function __construct(AccountingPlanRecord ...$accountingPlanRecords)
    {
        $this->id = uniqid();
        foreach ($accountingPlanRecords as $accountingPlanRecord) {
            $this->accountingPlanRecords[$accountingPlanRecord->getPlanId()] = $accountingPlanRecords;
        }
    }

    public function __clone(): void
    {
        $this->id = uniqid();
    }

    public function add(AccountingPlanRecord $accountingPlanRecord): AccountingPlanRecord
    {
        $this->accountingPlanRecords[$accountingPlanRecord->getPlanId()] = $accountingPlanRecord;

        return $this->accountingPlanRecords[$accountingPlanRecord->getPlanId()];
    }

    public function find($index): ?AccountingPlanRecord
    {
        return $this->accountingPlanRecords[$index] ?? null;
    }

    public function all(): array
    {
        return $this->accountingPlanRecords;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'records' => []
        ];
        foreach ($this->accountingPlanRecords as $accountingPlanRecord) {
            $data['records'][] = $accountingPlanRecord->toArray();
        }
        return $data;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(),JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
