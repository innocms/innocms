{{-- Laravel Envoy 部署配置 — InnoCMS 生产环境

服务器：www.innocms.com (175.178.76.173, ubuntu 用户)
  - SSH 别名 ~/.ssh/config: innocn
  - 应用目录 /www/wwwroot/www.innocms.com (www:www 拥有)
  - 主仓库 git@github.com:innocms/innocms.git (HTTPS 拉取)
  - 主题子仓库 themes/* 各自独立 git (cmsthemes/aurora, cmsthemes/funnlinkcn ...)
  - 数据库备份 /home/ubuntu/backups/

用法：
  envoy run pull             只拉主仓库代码（git fetch + reset --hard）
  envoy run pull_themes      只拉 themes/ 下所有子仓库（独立 git 仓库），并 view:clear
  envoy run deploy           标准部署（pull 主仓库 + migrate + cache）
  envoy run deploy_full      含 themes + composer install + npm build（依赖/前端/主题变更时用）
  envoy run backup_db        单独备份数据库
  envoy run rollback         回滚到上一个 commit

提示：
  - 只改了主题 blade / scss → push 对应 themes 子仓库后跑 envoy run pull_themes
  - 改了主仓库代码 → push github 后跑 envoy run pull 或 deploy
  - migrate 前会自动备份数据库，保留最近 30 份。--}}

@servers([
    'production' => 'innocn',
])

@setup
    $app_dir    = '/www/wwwroot/www.innocms.com';
    $backup_dir = '/home/ubuntu/backups';
@endsetup

@story('deploy')
    backup_db
    pull_code
    run_migrate
    clear_cache
@endstory

@story('pull')
    pull_code
@endstory

@story('deploy_full')
    backup_db
    pull_code
    pull_themes
    run_composer
    build_frontend
    run_migrate
    clear_cache
@endstory

@task('backup_db')
    echo 'Backing up database...'
    mkdir -p {{ $backup_dir }}
    cd {{ $app_dir }}

    set -a; source .env; set +a

    BACKUP_FILE="{{ $backup_dir }}/innocms_$(date +%Y%m%d_%H%M%S).sql"
    mysqldump \
        --single-transaction \
        --quick \
        --routines \
        --triggers \
        -h"${DB_HOST:-127.0.0.1}" \
        -P"${DB_PORT:-3306}" \
        -u"${DB_USERNAME}" \
        -p"${DB_PASSWORD}" \
        "${DB_DATABASE}" > "${BACKUP_FILE}"

    ls -t {{ $backup_dir }}/innocms_*.sql 2>/dev/null | tail -n +31 | xargs -r rm
    echo "Backup saved: ${BACKUP_FILE} ($(du -h ${BACKUP_FILE} | cut -f1))"
@endtask

@task('pull_code')
    echo 'Pulling latest main repo code from origin/main...'
    cd {{ $app_dir }}
    sudo -u www git config --global --add safe.directory {{ $app_dir }}
    sudo -u www git fetch origin main
    sudo -u www git reset --hard origin/main
    sudo -u www git log -1 --oneline
@endtask

@task('pull_themes')
    echo 'Pulling all theme subrepos under themes/ ...'
    cd {{ $app_dir }}/themes
    for t in */; do
        name="${t%/}"
        repo="{{ $app_dir }}/themes/$name"
        if [ -d "$repo/.git" ]; then
            echo "  -> $name"
            sudo -u www git config --global --add safe.directory "$repo"
            cd "$repo"
            sudo -u www git fetch origin
            sudo -u www git reset --hard origin/HEAD
            sudo -u www git log -1 --oneline
        fi
    done
    cd {{ $app_dir }}
    sudo -u www php artisan view:clear
@endtask

@task('run_composer')
    echo 'Installing composer dependencies (no-dev)...'
    cd {{ $app_dir }}
    sudo -u www composer install --prefer-dist --no-dev -o --no-interaction --no-progress
@endtask

@task('run_migrate')
    echo 'Running migrations...'
    cd {{ $app_dir }}
    sudo -u www php artisan migrate --force
@endtask

@task('build_frontend')
    echo 'Building frontend assets...'
    cd {{ $app_dir }}
    sudo -u www npm ci --no-audit --no-fund --silent
    sudo -u www npm run prod
@endtask

@task('clear_cache')
    echo 'Clearing and re-caching...'
    cd {{ $app_dir }}
    sudo -u www php artisan optimize:clear
    sudo -u www php artisan config:cache
    sudo -u www php artisan route:cache
    sudo -u www php artisan view:cache
    sudo -u www php artisan event:cache
@endtask

@task('rollback')
    echo 'Rolling back to previous commit...'
    cd {{ $app_dir }}
    sudo -u www git log --oneline -3
    sudo -u www git reset --hard HEAD^
    sudo -u www php artisan optimize:clear
    echo 'Note: migrations NOT rolled back. Manual rollback required if needed.'
@endtask

@finished
    echo "Done at " . date("Y-m-d H:i:s");
@endfinished
