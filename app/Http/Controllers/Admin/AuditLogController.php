<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(50);

        return view('admin.audit-logs.index', ['logs' => $logs]);
    }

    public function show(AuditLog $log)
    {
        $log->load('user');

        return view('admin.audit-logs.show', ['log' => $log]);
    }
}
