<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public function log(string $entity, string $entityId, string $action, ?array $previous, ?array $current): void
    {
        AuditLog::create([
            'entity' => $entity,
            'entity_id' => $entityId,
            'action' => $action,
            'previous_data' => $previous,
            'current_data' => $current,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}
