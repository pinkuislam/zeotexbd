<?php

namespace Oshnisoft\HRM\Models\Traits;

trait HasCreatedByAndUpdatedBy
{
    public function createdBy()
    {
        return $this->belongsTo(config('hrm.models.created_by'), 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(config('hrm.models.updated_by'), 'updated_by', 'id');
    }
}
