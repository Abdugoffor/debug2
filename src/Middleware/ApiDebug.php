<?php

namespace ApiMiddleware\ApiDebug\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiDebug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $queries = [];

        DB::listen(function ($query) use (&$queries) {
            $queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ];
        });

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $duration = $endTime - $startTime;
        $memoryUsage = ($endMemory - $startMemory) / 1024 / 1024;

        $data = [
            'url' => $request->url(),
            'davomiyligi' => $duration . ' sekund',
            'xotiradan_foydalanish' => $memoryUsage . ' MB',
            'so`rovlar_soni' => count($queries) . ', jami yuborilgan so`rovlar soni',
            'so`rovlar' => $queries,
        ];

        if (method_exists($response, 'getData')) {
            $originalData = $response->getData(true);
            $mergedData = array_merge($originalData, ['ApiDebug' => $data]);
            $response->setData($mergedData);
        } else {
            Log::info('API profiling', $data);
        }

        return $response;
    }
}
