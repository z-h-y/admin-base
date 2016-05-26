<?php namespace App\Http\Controllers\Api;

use DB;
use Validator;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Database\Query\Builder;
use Illuminate\Routing\Controller;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;

abstract class ApiBaseController extends Controller {

    use DispatchesCommands;

    protected $connection = Constants::CONN_DEFAULT; // 默认的数据库链接名称

    protected $tableName = ''; // 当前控制器对应的数据表名称

    protected $hiddenKeys = array(); // 在前端不需要展示的字段

    protected $updateFlag = false; // 是否为更新(update)模式

    protected $rules = array(); // 参数校验规则

    protected $pagination = array(); // 分页数据

    /**
     * Validate request
     *
     * 根据定义好的规则对请求参数进行校验
     *
     * @return array|null
     */
    protected function validate()
    {
        $validator = Validator::make(\Request::all(), $this->rules);
        if ($validator->fails()) {
            return $validator->messages()->toArray();
        }
        return null;
    }

    /**
     * Success response
     *
     * 成功响应，在表明某个动作执行成功时调用
     *
     * @param bool $success
     * @return Response
     */
    protected function successResponse($success = true)
    {
        return response()->json(array('data' => array('success' => $success)), 200, array(), JSON_NUMERIC_CHECK);
    }

    /**
     * Error response
     *
     * 错误响应，在发送错误时调用
     *
     * @param $msg
     * @param $debugMsg
     * @return Response
     */
    protected function errorResponse($msg, $debugMsg = '')
    {
        $messages = array('message' => $msg);
        if ($debugMsg) {
            $messages['debugMessage'] = 'Error debug: ' . $debugMsg;
        }
        return response()->json(array('error' => $messages, 'data' => array()));
    }

    /**
     * Make json data response
     *
     * 创建JSON格式的响应，默认的格式为:  { data: [{}, {}] }
     *
     * @param $data
     * @param array $hiddenKeys
     * @param boolean $ignoreDataFormat
     * @return Response
     */
     protected function jsonResponse($data, $hiddenKeys = array(), $ignoreDataFormat = false)
     {
         $single = false;
         if (!isset($data[0]) || !is_array($data[0])) {
             $data = array($data);
             $single = true;
         }
         $hiddenKeys = !empty($hiddenKeys) ? $hiddenKeys : $this->hiddenKeys;
         if ($data && $hiddenKeys) {
             $data = collect($data)->map(function($item) use ($hiddenKeys) {
                 return array_except($item, $hiddenKeys);
             });
         }
         if ($single) {
             $data = $data[0];
         }
         $result = array('data' => $data);
         if ($this->pagination) {
             $result['pagination'] = $this->pagination;
         }
         if ($ignoreDataFormat) {
             return response()->json($result, 200, array('ignore-data-format' => $ignoreDataFormat), JSON_NUMERIC_CHECK);
         } else {
             return response()->json($result, 200, array(), JSON_NUMERIC_CHECK);
         }
     }

    /**
     * Get the DB query builder
     *
     * 获取当前控制器对应数据表的查询接口
     *
     * @return Builder|null
     */
    protected function getQuery()
    {
        if ($this->connection && $this->tableName) {
            $builder = DB::connection($this->connection);
            if ($this->tableName) {
                if (\Request::has('pagination')) {
                    $this->pagination['count'] = $builder->table($this->tableName)->count();
                }
                return $builder->table($this->tableName);
            }
        }
    }

    /**
     * Apply query filter
     *
     * 如果请求参数中包含有filter（一个json字符串），就将filter中的所有参数作为查询条件
     *
     * @param Builder $builder
     * @return Builder|null
     */
    protected function applyFilter(Builder $builder)
    {
        $filter = \Request::input(Constants::FILTER_NAME);

        if ($builder && $filter) {
            $filterArr = json_decode($filter);
            if ($filterArr) {
                foreach ($filterArr as $key => $val) {
                    $fuzzy = false;
                    $index = strpos($key, Constants::FUZZY_PREFIX);
                    if ($index === 0) {
                        $fuzzy = true;
                        $key = substr($key, strlen(Constants::FUZZY_PREFIX));
                    }
                    if ($fuzzy) {
                        $builder->where($key, 'LIKE', '%'.$val.'%');
                    } else {
                        $builder->where($key, '=', $val);
                    }
                }
            }
        }
        return $builder;
    }

    /**
     * Use the query builder to fetch data
     *
     * 用构造好的query builder查询数据，并添加分页信息
     *
     * @param Builder $builder
     * @param bool $pagination
     * @param Closure $fn
     * @return Response
     */
    protected function buildResponse(Builder $builder, $pagination = false, $fn = null)
    {
        if ($builder) {
            if (\Request::has('pagination')) {
                $pagination = true;
            }
            if ($pagination) {
                $page = \Request::input('page', 1);
                $perPage = \Request::input('perPage', 10);
                $this->pagination['page'] = $page;
                $this->pagination['perPage'] = $perPage;
                $skip = $perPage * ($page - 1);
                $builder->skip($skip)->take($perPage);
            }

            $sort = \Request::input('order'); // 'asc' or 'desc'
            $column = \Request::input('sortBy');
            if ($sort && $column) {
                $builder->orderBy($column, $sort);
            }

            $limit = \Request::input('limit');
            if ($limit) {
                $builder->limit(intval($limit));
            }

            $result = $builder->get();
            if ($fn && is_callable($fn)) {
                return $this->jsonResponse($fn($result));
            } else {
                return $this->jsonResponse($result);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->jsonResponse($this->getQuery()->find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if ($id) {
            $this->getQuery()->where('id', '=', $id)->delete();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->buildResponse($this->applyFilter($this->getQuery()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
    }
}
