<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Utils\Constants;
use App\Utils\Utils;
use Carbon\Carbon;

class DateRangeFilter {

    // 检查查询参数中的开始结束日期
    public function handle(Request $request, Closure $next)
    {
        $filter = $request->input(Constants::FILTER_NAME);
        if ($filter) {
            $filterArr = json_decode($filter, true);
            if (isset($filterArr['startDate']) || isset($filterArr['endDate'])) {
                $startDate = Utils::safeGet($filterArr, 'startDate');
                $endDate = Utils::safeGet($filterArr, 'endDate');
                if ($startDate && $endDate) {
                    $startDate = Carbon::parse($startDate);
                    $endDate = Carbon::parse($endDate);

                    if ($endDate->lt($startDate)) {
                        return response(array(
                            'error' => array(
                                'message' => '结束日期不能小于开始日期!',
                            )
                        ), 400);
                    }
                    $days = $endDate->diffInDays($startDate);
                    if ($days > 180) {
                        return response(array(
                            'error' => array(
                                'message' => '开始结束日期间隔不能大于半年!',
                            )
                        ), 400);
                    }
                }
            }
        }

        return $next($request);
    }

}
