<?php

namespace App\Domains\Audit\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public function log(
        string $eventType, 
        Model $model, 
        ?array $oldData = null, 
        ?array $newData = null
    ): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public function logModelUpdate(Model $model, array $changes): void
    {
        $oldData = collect($changes)->mapWithKeys(fn($value, $key) => [
            $key => $model->getOriginal($key)
        ])->toArray();

        $this->log(
            'updated', 
            $model, 
            $oldData, 
            $changes
        );
    }
}