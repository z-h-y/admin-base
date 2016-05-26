(function() {
  'use strict';

  angular
    .module('adminApp')
    .factory('Config', Config);

  Config.$inject = ['DTOptionsBuilder'];

  function Config(DTOptionsBuilder) {

    var appTitle = 'AppGame 后台基础框架';

    var menuList = [
      {
        name: 'dashboard',
        displayName: 'Dashboard',
        url: 'app.dashboard',
        icon: 'fa-dashboard'
      },

      /********* Add your app's menu item here *************/



      /********* Add your app's menu item here *************/

      {
        name: 'admin',
        displayName: '后台管理',
        url: 'app.admin',
        icon: 'fa-cog',
        children: [
          {
            name: 'user',
            displayName: '用户设置',
            url: 'app.admin.user'
          },
          {
            name: 'role',
            displayName: '角色设置',
            url: 'app.admin.role'
          },
          {
            name: 'permission',
            displayName: '权限设置',
            url: 'app.admin.permission'
          },
          {
            name: 'codec',
            displayName: '编码设置',
            url: 'app.admin.codec'
          }
        ]
      }
    ];

    var tableWithLanguage = {
                              'sProcessing':   '处理中...',
                              'sLengthMenu':   '显示 _MENU_ 项结果',
                              'sZeroRecords':  '没有匹配结果',
                              'sInfo':         '显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项',
                              'sInfoEmpty':    '显示第 0 至 0 项结果，共 0 项',
                              'sInfoFiltered': '(由 _MAX_ 项结果过滤)',
                              'sInfoPostFix':  '',
                              'sSearch':       '搜索:',
                              'sUrl':          '',
                              'sEmptyTable':     '表中数据为空',
                              'sLoadingRecords': '载入中...',
                              'sInfoThousands':  ',',
                              'oPaginate': {
                                  'sFirst':    '首页',
                                  'sPrevious': '上页',
                                  'sNext':     '下页',
                                  'sLast':     '末页'
                              },
                              'oAria': {
                                  'sSortAscending':  ': 以升序排列此列',
                                  'sSortDescending': ': 以降序排列此列'
                              }
                            };

    var multiSelectWithLanguage = {
                                    'selectAll' : '全选',
                                    'selectNone' : '全不选',
                                    'reset' : '重置',
                                    'search' : '搜索',
                                    'nothingSelected' : '未选择'
                                  };
    var datePickerLanguage = {
                            cancelLabel: '取消',
                            applyLabel: '确定',
                            customRangeLabel: '自定义',
                            daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
                          };

    function getDtOptions(cb) {
      var result = DTOptionsBuilder
                  .newOptions()
                  .withPaginationType('full_numbers')
                  .withDisplayLength(10)
                  .withBootstrap()
                  .withLanguage(tableWithLanguage);
      if (cb) {
        return cb(result);
      }
      return result;
    }

    function configRoutes (routes) {
      /********* Add your app's route config here *************/



      /********* Add your app's route config here *************/
      return routes;
    }

    function prepareDynamicMenu () {
      /********* Prepare your app's dynamic menu here *************/



      /********* Prepare your app's dynamic menu here *************/
    }

    return {
      appTitle : appTitle,
      prepareDynamicMenu: prepareDynamicMenu,
      configRoutes: configRoutes,
      menuList: menuList,
      getDtOptions: getDtOptions,
      tableWithLanguage: tableWithLanguage,
      multiSelectWithLanguage: multiSelectWithLanguage,
      datePickerLanguage: datePickerLanguage,
      basePath: 'admin/api'
    };

  }

})();
