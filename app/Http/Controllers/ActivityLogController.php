<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function getLogs(Request $request)
    {
        try {
            $page = $request->get('page', 0);
            $size = $request->get('size', 30);
            $skip = $page * $size;

            $query = ActivityLog::query();
            $count = $query->count();

            $logs = $query->skip($skip)->take($size)->get();

            return response()->json([
                'success' => true,
                'page' => $page,
                'size' => $size,
                'count' => $count,
                'logs' => $logs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
