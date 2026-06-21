<?php

namespace App\Models\ImplementationTracker;

use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function booted()
    {
        static::updated(function ($model) {
            $changedFields = $model->getChanges();
            
            // Skip if no changes or only timestamps changed
            unset($changedFields['updated_at']);
            if (empty($changedFields)) {
                return;
            }

            $user = Auth::user();
            $changedByName = $user ? $user->name : 'System';

            // Find tab name and row reference based on class
            $tabName = '';
            $rowRef = '';
            
            if ($model instanceof DataImplementation) {
                $tabName = 'Data Implementation';
                $rowRef = $model->module_name;
            } elseif ($model instanceof TemplateImplementation) {
                $tabName = 'Template Implementation';
                $rowRef = $model->template_name;
            } elseif ($model instanceof Integration) {
                $tabName = 'Integrations';
                $rowRef = $model->integration_name;
            } elseif ($model instanceof Training) {
                $tabName = 'Training';
                $rowRef = $model->module_name;
            }

            foreach ($changedFields as $field => $newValue) {
                if ($field === 'created_at' || $field === 'updated_at') {
                    continue;
                }

                $oldValue = $model->getOriginal($field);

                $oldStr = is_array($oldValue) ? json_encode($oldValue) : (string)$oldValue;
                $newStr = is_array($newValue) ? json_encode($newValue) : (string)$newValue;

                ImplActivityLog::create([
                    'school_id' => $model->school_id,
                    'tab_name' => $tabName,
                    'row_reference' => $rowRef,
                    'field_changed' => ucwords(str_replace('_', ' ', $field)),
                    'old_value' => $oldStr,
                    'new_value' => $newStr,
                    'changed_by' => $changedByName,
                    'changed_at' => now(),
                ]);
            }
        });
    }
}
