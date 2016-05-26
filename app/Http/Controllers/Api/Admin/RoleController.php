<?php namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Role;
use App\Http\Controllers\Api\ApiBaseController;

class RoleController extends ApiBaseController {

    protected $tableName = 'roles';

    protected $role;

    protected $rules = array(
        'name' => 'required|min:3',
        'displayName' => 'required|min:2',
        'description' => 'min:2',
    );

    public function __construct(Role $role)
    {
        $this->role = $role;
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

        $role = $this->role;
        $role->name = isset($name) ? $name : $role->name;
        $role->displayName = isset($displayName) ? $displayName : $role->displayName;
        $role->description = isset($description) ? $description : $role->description;

        $error = '';
        if (!$this->updateFlag) {
            $existsRole = Role::where('name', '=', $role->name)->first();
            if ($existsRole) {
                $error = 'Role already exists!';
            }
        }

        if (!$error) {
            $role->save();
            if ($role->id) {
                return $this->show($role->id);
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
            $this->role = Role::find($id);
            if ($this->role) {
                $this->updateFlag = true;
                return $this->store($request);
            }
        }
    }

    /**
     * Get role's permissions
     *
     * @param $id
     * @return Response
     */
    public function getPermissions($id) {
        $role = Role::find($id);
        return $this->jsonResponse($role->perms->toArray());
    }

    /**
     * Update role's permissions
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function updatePermissions(Request $request, $id) {
        $role = Role::find($id);
        $ids = $request->input('ids');
        if ($ids) {
            $perIds = array_map(function($item) {
                return intval(trim($item));
            }, explode(',', $ids));
        } else {
            $perIds = array();
        }

        if ($role) {
            $role->perms()->sync($perIds);
            return $this->jsonResponse($role);
        }
    }

    /**
     * Get role's users
     *
     * @param $id
     * @return Response
     */
    public function getUsers($id) {
        $role = Role::find($id);
        return $this->jsonResponse($role->users->toArray(), array('pivot', 'remember_token'));
    }
}
