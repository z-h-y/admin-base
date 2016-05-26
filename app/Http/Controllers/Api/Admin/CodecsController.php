<?php namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Codec;
use App\Http\Controllers\Api\ApiBaseController;
use DB;

class CodecsController extends ApiBaseController {

    protected $tableName = 'codecs';

    protected $codec;

    protected $rules = array(
        'group' => 'required|min:3',
        'name' => 'required|min:1',
    );

    public function __construct(Codec $codec)
    {
        $this->codec = $codec;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validate = $this->validate();
        if ($validate) {
            return $this->errorResponse($validate);
        }

        $name = $request->input('name');
        $group = $request->input('group');
        $comment = $request->input('comment');
        $active = ($request->input('active') == 1) ? 1 : 0;
        $value = $request->input('value');

        $codec = $this->codec;
        $codec->name = isset($name) ? $name : $codec->name;
        $codec->group = isset($group) ? $group : $codec->group;
        $codec->comment = isset($comment) ? $comment : $codec->comment;
        $codec->active = isset($active) ? $active : $codec->active;
        $codec->value = isset($value) ? $value : $codec->value;

        $error = '';
        if (!$this->updateFlag) {
            if ($value) {
                $existsCodec = DB::table('codecs')->where('group', $group)->where('value', $value)->first();
                if ($existsCodec) {
                    $error = 'Codec with same value already exists!';
                }
            } else {
                $existsValue = DB::table('codecs')->where('group', '=', $codec->group)->max('value');
                if ($existsValue) {
                    $codec->value = $existsValue + 1;
                } else {
                    $codec->value = 1;
                }
            }
        }

        if (!$error) {
            $codec->save();
            if ($codec->id) {
                return $this->show($codec->id);
            }
        } else {
            return $this->errorResponse($error);
        }
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
        if ($id) {
            $this->codec = Codec::find($id);
            if ($this->codec) {
                $this->updateFlag = true;
                return $this->store($request);
            }
        }
    }
}
