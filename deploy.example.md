### 1. 在项目根目录下新建一个.env文件，并从.env.example文件中复制所有的内容；

### 2.填写数据库配置，包括后台MySQL、后台MongoDB以及LeanCloud备份数据库（也是mongo，跟后台mongo可以在一起，不同数据库即可），以下只是示例：

    #后台MySQL
    DB_MYSQL_HOST=localhost
    DB_MYSQL_DATABASE=db_name
    DB_MYSQL_USERNAME=username
    DB_MYSQL_PASSWORD=secret

    #后台MongoDB
    DB_MONGO_HOST=localhost
    DB_MONGO_DATABASE=db_name
    DB_MONGO_PORT=27017
    DB_MONGO_USERNAME=
    DB_MONGO_PASSWORD=

    #LeanCloud数据备份 - MongoDB
    DB_LC_BACKUP_HOST=localhost
    DB_LC_BACKUP_DATABASE=db_lc_backup
    DB_LC_BACKUP_PORT=27017
    DB_LC_BACKUP_USERNAME=
    DB_LC_BACKUP_PASSWORD=

### 3.按照以下内容配置LeanCloud，并请联系项目负责人获取 LEANCOULD_APP_ID，LEANCOULD_APP_KEY，LEANCOULD_MASTER_KEY ：

    LEANCOULD_APP_URL=https://api.leancloud.cn/1.1
    LEANCOULD_APP_ID=
    LEANCOULD_APP_KEY=
    LEANCOULD_MASTER_KEY=
    LEANCOULD_BACKUP_INTERVAL=4
    LEANCOULD_BACKUP_LIST=

### 4.进入项目的storage文件夹，按照以下结构创建对应的文件夹，并确保web server对它们有读写权限:

    storage
      -app
      -framework
        -cache
        -sessions
        -views
      -logs

### 5.初始化数据库，在项目根目录下运行以下指令：

    php artisan key:generate
    php artisan migrate
    php artisan db:seed

### 6.添加以下cron job，注意将 /path/to/artisan 替换为 **项目根目录下artisan** 文件的完整路径！

    * * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1


******

## 备注
配置过程中，如果遇到错误或者不清楚的地方，请运维人员联系项目负责人进行确认！


更新于 2015-09-17
