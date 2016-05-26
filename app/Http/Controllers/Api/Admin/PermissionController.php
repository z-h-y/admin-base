<?php namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Permission;
use App\Http\Controllers\Api\ApiBaseController;

class PermissionController extends ApiBaseController {

    protected $tableName = 'permissions';

    protected $permission;

    protected $rules = array(
        'name' => 'required|min:3',
        'displayName' => 'required|min:2',
        'description' => 'min:2',
    );

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
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
        $displayName = $request->input('displayName');
        $description = $request->input('description');

        $permission = $this->permission;
        $permission->name = isset($name) ? $name : $permission->name;
        $permission->displayName = isset($displayName) ? $displayName : $permission->displayName;
        $permission->description = isset($description) ? $description : $permission->description;

        $error = '';
        if (!$this->updateFlag) {
            $existsPermission = Permission::where('name', '=', $permission->name)->first();
            if ($existsPermission) {
                $error = 'Permission already exists!';
            }
        }

        if (!$error) {
            $permission->save();
            if ($permission->id) {
                return $this->show($permission->id);
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
            $this->permission = Permission::find($id);
            if ($this->permission) {
                $this->updateFlag = true;
                return $this->store($request);
            }
        }
    }

    /**
     * Get permission's roles
     *
     * @param $id
     * @return Response
     */
    public function getRoles($id) {
        $permission = Permission::find($id);
        return $this->jsonResponse($permission->roles->toArray(), array('pivot'));
    }
}
