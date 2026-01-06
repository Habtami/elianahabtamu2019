<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'alert_days_before',
        'performance_guarantee_amount',
        'performance_guarantee_end_date',
        'performance_guarantee_alert_days',
        'contract_end_date_edit_count',
        'performance_guarantee_edit_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'performance_guarantee_end_date' => 'date',
        'performance_guarantee_amount' => 'decimal:2',
    ];

    /**
     * Get the contract histories for the contract.
     */
    public function histories()
    {
        return $this->hasMany(ContractHistory::class);
    }

    /**
     * Get contract end date extension/edit history
     */
    public function contractEndDateHistories()
    {
        return $this->histories()->where('field_name', 'contract_end_date');
    }

    /**
     * Get performance guarantee extension/edit history
     */
    public function performanceGuaranteeHistories()
    {
        return $this->histories()->where('field_name', 'performance_guarantee');
    }

    /**
     * Check if contract end date alert should be triggered
     */
    public function shouldAlertContractEndDate()
    {
        if (!$this->end_date) {
            return false;
        }

        $alertDate = Carbon::parse($this->end_date)->subDays($this->alert_days_before);
        return Carbon::now()->greaterThanOrEqualTo($alertDate) && Carbon::now()->lessThan($this->end_date);
    }

    /**
     * Check if performance guarantee alert should be triggered
     */
    public function shouldAlertPerformanceGuarantee()
    {
        if (!$this->performance_guarantee_end_date) {
            return false;
        }

        $alertDate = Carbon::parse($this->performance_guarantee_end_date)
            ->subDays($this->performance_guarantee_alert_days);
        return Carbon::now()->greaterThanOrEqualTo($alertDate) &&
               Carbon::now()->lessThan($this->performance_guarantee_end_date);
    }

    /**
     * Get days remaining until contract end date
     */
    public function getDaysUntilContractEnd()
    {
        if (!$this->end_date) {
            return null;
        }

        return Carbon::now()->diffInDays(Carbon::parse($this->end_date), false);
    }

    /**
     * Get days remaining until performance guarantee end date
     */
    public function getDaysUntilPerformanceGuaranteeEnd()
    {
        if (!$this->performance_guarantee_end_date) {
            return null;
        }

        return Carbon::now()->diffInDays(Carbon::parse($this->performance_guarantee_end_date), false);
    }

    /**
     * Update contract end date and log history
     */
    public function updateContractEndDate($newEndDate, $reason = null, $editedBy = null)
    {
        $oldEndDate = $this->end_date;

        ContractHistory::create([
            'contract_id' => $this->id,
            'field_name' => 'contract_end_date',
            'old_value' => $oldEndDate ? $oldEndDate->format('Y-m-d') : null,
            'new_value' => $newEndDate,
            'edited_by' => $editedBy,
            'reason' => $reason,
        ]);

        $this->end_date = $newEndDate;
        $this->contract_end_date_edit_count++;
        $this->save();
    }

    /**
     * Update performance guarantee and log history
     */
    public function updatePerformanceGuarantee($newAmount, $newEndDate, $reason = null, $editedBy = null)
    {
        $oldData = [
            'amount' => $this->performance_guarantee_amount,
            'end_date' => $this->performance_guarantee_end_date ?
                         $this->performance_guarantee_end_date->format('Y-m-d') : null,
        ];

        $newData = [
            'amount' => $newAmount,
            'end_date' => $newEndDate,
        ];

        ContractHistory::create([
            'contract_id' => $this->id,
            'field_name' => 'performance_guarantee',
            'old_value' => json_encode($oldData),
            'new_value' => json_encode($newData),
            'edited_by' => $editedBy,
            'reason' => $reason,
        ]);

        $this->performance_guarantee_amount = $newAmount;
        $this->performance_guarantee_end_date = $newEndDate;
        $this->performance_guarantee_edit_count++;
        $this->save();
    }
}
