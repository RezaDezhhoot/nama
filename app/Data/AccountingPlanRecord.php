<?php

namespace App\Data;

class AccountingPlanRecord
{
    /**
     * Create a new class instance.
     */
    protected $ids = [];
    public function __construct(protected $plan ,protected $planId , protected $count = 0, protected $students = 0 , protected $totalFinalAmount = 0)
    {
        //
    }

    public function getPlan(): string
    {
        return $this->plan;
    }

    public function getCount(): int|string
    {
        return $this->count;
    }

    public function addCount(int|string $count = 1): static
    {
        $this->count += $count;

        return $this;
    }

    public function getStudents(): int|string
    {
        return $this->students;
    }

    public function addStudents(int|string $students): static
    {
        $this->students += $students;

        return $this;
    }

    public function getTotalFinalAmount(): int|string
    {
        return $this->totalFinalAmount;
    }

    public function addTotalFinalAmount(int|string $totalFinalAmount): static
    {
        $this->totalFinalAmount += $totalFinalAmount;

        return $this;
    }

    public function addIds(int $id): static
    {
        $this->ids[] = $id;

        return $this;
    }


    public function getPlanId(): int|string
    {
        return $this->planId;
    }

    public  function toArray(): array
    {
        return [
            'plan' => $this->plan,
            'plan_id' => $this->planId,
            'count' => $this->count,
            'students' => $this->students,
            'totalFinalAmount' => $this->totalFinalAmount,
            'ids' => $this->ids
        ];
    }
}
