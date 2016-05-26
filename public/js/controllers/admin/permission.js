(function() {
  'use strict';

  angular
    .module('adminApp')
    .controller('PermissionCtrl', PermissionCtrl);

  PermissionCtrl.$inject = ['$scope', 'DTColumnDefBuilder', 'Config', 'Permission', 'Restangular', '$window', 'Utils', 'Codec'];

  function PermissionCtrl ($scope, DTColumnDefBuilder, Config, Permission, Restangular, $window, Utils, Codec) {

    $scope.dtOptions = Config.getDtOptions();
    $scope.dtColumnDefs = [
      DTColumnDefBuilder.newColumnDef(4).notSortable()
    ];
    $scope.dtInstanceCb = Utils.buildDtInstanceCb($scope, _clearPermission);
    $scope.cancelEdit = cancelEdit;
    $scope.editPermission = editPermission;
    $scope.savePermission = savePermission;
    $scope.changeType= changeType;
    $scope.permissionType = [];

    Permission.getList().then(function(permissionList) {
      $scope.permissionList = permissionList;
      var arr = [];
      _.forEach($scope.permissionList, function(n) {
        var displayName = n.displayName.split(':').slice(0,2).join(':');
        if (arr.indexOf(displayName) === -1) {
          arr.push(displayName);
          var type = {};
          type.displayName = displayName;
          type.name = n.name.split(':').slice(0,2).join(':');
          $scope.permissionType.push(type);
        }
      });

      // Codec.getList().then(function(CodecList) {
      //   $scope.permissionType = _.filter(CodecList, {'group': 'permissionType'});
      //   _.forEach($scope.permissionList, function(n) {
      //     n.typeName = _.result(_.find($scope.permissionType, {'value' : n.type}), 'name');
      //   });
      // });

    });

    var permissionTable;

    function changeType(selectedId) {
      permissionTable = permissionTable ? permissionTable : $('#permissionTable').DataTable();
      $scope.permission = {};
      if (selectedId) {
        permissionTable.search(selectedId.displayName+ ':').draw();  
        $scope.permission.displayName = selectedId.displayName + ':';
        $scope.permission.name = selectedId.name + ':';
      } else {
        permissionTable.search('').draw();  
      }
    }

    var permissionIndex = -1;

    function _clearPermission() {
      $scope.permission = {};
      permissionIndex = -1;
      $scope.selectedId = '';
    }

    function cancelEdit() {
      _clearPermission();
    }

    function editPermission(index) {
      permissionIndex = index;
      var permission = Restangular.copy($scope.permissionList[permissionIndex]);
      // $scope.selectedId = _.filter($scope.permissionType, {'value': permission.type})[0].id;
      if (permission) {
        if (permission.readOnly) {
          var isConfirm = $window.confirm('此权限是系统自动生成的，确定要对其进行修改?');
          if (isConfirm) {
            $scope.permission = permission;
          } else {
            _clearPermission();
          }
        } else {
            $scope.permission = permission;
        }
      }
    }

    function savePermission() {
      var permission = $scope.permission;
      // var permissionType = '';
      if (permission && permission.name) {
        // permissionType = _.filter($scope.permissionType, {'id': $scope.selectedId})[0];
        // permission.type = permissionType.value;
        // permission.typeName = permissionType.name;
        if (!permission.id) {
          Permission.post(permission).then(function(newPermission) {
            if (!newPermission.error) {
              // newPermission.typeName = permissionType.name;
              $scope.permissionList.push(newPermission);
            }
            _clearPermission();
          }, function() {
            _clearPermission();
          });
        } else {
          permission.save().then(function(data) {
            if (!data.error) {
              $scope.permissionList.splice(permissionIndex, 1, permission);
            }
            _clearPermission();
          }, function() {
            _clearPermission();
          });
        }
      } else {
        Utils.displayError('请填写完整*号必填项');
      }
    }
  }
})();
