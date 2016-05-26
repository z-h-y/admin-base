## admin-base
本项目是后台项目基础框架，使用Laravel5与AngularJS搭建。
本项目遵循前后端分离，后端只提供rest api，前端是一个web app。

## 设置步骤
## 0.服务器配置

    PHP >= 5.5.9
    OpenSSL PHP Extension
    PDO PHP Extension
    Mbstring PHP Extension
    Tokenizer PHP Extension
    mcrypt PHP Extension
    MongoDB PHP Extension


## 1.修改配置文件
在项目根目录下新建一个.env文件，并从.env.example文件中复制所有的内容；

### 环境设置
生产环境，必须这样设置：

    APP_ENV=production
    APP_DEBUG=false

开发或测试环境，建议设置如下：

    APP_ENV=local
    APP_DEBUG=true

### 数据库
设置本地MySQL配置，示例如下：

    #后台MySQL
    DB_MYSQL_HOST=localhost
    DB_MYSQL_DATABASE=db_name
    DB_MYSQL_USERNAME=username
    DB_MYSQL_PASSWORD=secret

其他数据库配置，请查看本项目wiki里的env配置页面

## 2.文件夹与权限设置
请进入storage文件夹，按照以下结构创建对应的文件夹:

    storage
      -app
      -framework
        -cache
        -sessions
        -views
      -logs

storage及其子文件，bootstrap/cache目录用于存储session、cache等，必须保证web服务器对它们具有可写权限

## 3.初始化数据库
在项目根目录下运行以下指令：

    php artisan key:generate
    php artisan migrate
    php artisan db:seed

## 4.设置Cron job (可选，依据项目情况而定，请与项目负责人进行确认)
添加以下cron job，注意将 /path/to/artisan 替换为artisan文件的完整路径（artisan一般在项目根目录下）

    * * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1


******

## 备注
以上说明包含了部分可选的设置，运维人员在配置服务器时，请务必联系项目负责人进行确认！


更新于 2015-08-27
