<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ front_route('home.index') }}">
  <title>安装引导</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ image_origin(system_setting('favicon', 'images/favicon.png')) }}">
  <link rel="stylesheet" href="{{ asset('build/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('build/install/css/app.css') }}">
  <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/layer/3.5.1/layer.js') }}"></script>
  @stack('header')
</head>

<body>
  <header>
    <div class="container d-flex justify-content-between">
      <div class="logo"><img src="{{ asset('images/logo.png') }}" class="img-fluid"></div>

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          {{ __('install::common.'.locale()) }}
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ route('install.install.index', ['locale' => 'zh_cn']) }}">中文</a></li>
          <li><a class="dropdown-item" href="{{ route('install.install.index', ['locale' => 'en']) }}">English</a></li>
        </ul>
      </div>
    </div>
  </header>
  <div class="container">
    <div class="install-box">
      <ul class="progress-wrap">
        <li class="active"><div class="icon"><span>1</span></div><div class="text">{{ __('install::common.license') }}</div></li>
        <li><div class="icon"><span>2</span></div><div class="text">{{ __('install::common.environment') }}</div></li>
        <li><div class="icon"><span>3</span></div><div class="text">{{ __('install::common.configuration') }}</div></li>
        <li><div class="icon"><span>4</span></div><div class="text">{{ __('install::common.completed') }}</div></li>
      </ul>
      <div class="install-wrap mb-4">
        <div class="install-1 install-item active">
          <div class="head-title">{{ __('install::common.open_source') }}</div>
          <div class="install-content" id="content">
            @include("install::license.".locale())
          </div>

          <div class="d-flex justify-content-center mt-4">
            <button type="button" class="btn btn-primary btn-lg next-btn">我已阅读并同意</button>
          </div>
        </div>

        <div class="install-2 install-item d-none">
          <div class="head-title">环境检测</div>
          <div class="install-content">
            <table class="table">
              <thead>
                <tr><th colspan="3" class="bg-light">环境检测</th></tr>
              </thead>
              <tbody>
                <tr><td>环境</td><td>当前</td><td>状态</td></tr>
                <tr>
                  <td>PHP版本(8.2+)</td>
                  <td>{{ $php_version }}</td>
                  <td><i class="bi {{ $php_env ? 'text-success bi-check-circle-fill' : 'bi-x-circle-fill text-danger' }}"></i></td>
                </tr>
                @foreach($extensions as $key => $value)
                <tr>
                  <td>{{ $key }}</td>
                  <td></td>
                  <td><i class="bi {{ $value ? 'text-success bi-check-circle-fill' : 'bi-x-circle-fill text-danger' }}"></i></td>
                </tr>
                @endforeach

              </tbody>

              <thead>
                <tr><th colspan="3" class="bg-light">权限检测</th></tr>
              </thead>
              <tbody>
                <tr><td>目录/文件</td><td>配置</td><td>状态</td></tr>
                @foreach($permissions as $key => $value)
                  <tr>
                  <td>{{ $key }}</td>
                  <td>755</td>
                  <td><i class="bi {{ $value ? 'text-success bi-check-circle-fill' : 'bi-x-circle-fill text-danger' }}"></i></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-center mt-4">
            <button type="button" class="btn btn-outline-secondary prev-btn me-3">上一步</button>
            <button type="button" class="btn btn-primary next-btn">下一步</button>
          </div>
        </div>

        <div class="install-3 install-item d-none">
          <div class="head-title">参数配置</div>
          <div class="install-content">
            <form class="needs-validation" novalidate>
              <div class="bg-light py-2 mb-2 text-center fw-bold">数据库配置</div>
              <div class="row gx-2">
                <div class="col-6">
                  <div class="mb-3">
                    <label for="type" class="form-label">数据库类型</label>
                    <select class="form-select sql-type" id="type" name="type" required>
                      <option value="mysql">MySQL</option>
                      <option value="sqlite">SQLite</option>
                    </select>
                    <div class="invalid-feedback">请选择数据库类型</div>
                  </div>
                </div>
                <div class="col-6 mysql-item">
                  <div class="mb-3">
                    <label for="host" class="form-label">主机地址</label>
                    <input type="text" class="form-control" id="host" name="db_hostname" required placeholder="请输入主机地址" value="127.0.0.1">
                    <div class="invalid-feedback">请输入主机地址</div>
                  </div>
                </div>
                <div class="col-6 mysql-item">
                  <div class="mb-3">
                    <label for="port" class="form-label">端口号</label>
                    <input type="text" class="form-control" id="port" name="db_port" required placeholder="请输入端口" value="3306">
                    <div class="invalid-feedback">请输入端口号</div>
                  </div>
                </div>
                <div class="col-6 mysql-item">
                  <div class="mb-3">
                    <label for="database" class="form-label">数据库名</label>
                    <input type="text" class="form-control" id="database" name="db_name" required placeholder="请输入数据库名">
                    <div class="invalid-feedback">请输入数据库名</div>
                  </div>
                </div>
                <div class="col-6 mysql-item">
                  <div class="mb-3">
                    <label for="database" class="form-label">表前缀</label>
                    <input type="text" class="form-control" id="db_prefix" name="db_prefix" value="icms_" required placeholder="请输入表前缀">
                    <div class="invalid-feedback">请输入表前缀</div>
                  </div>
                </div>
                <div class="col-6 mysql-item">
                  <div class="mb-3">
                    <label for="username" class="form-label">数据库账号</label>
                    <input type="text" class="form-control" id="username" name="db_username" required placeholder="请输入数据库账号">
                    <div class="invalid-feedback">请输入数据库账号</div>
                  </div>
                </div>
                <div class="col-6 mysql-item">
                  <div class="mb-3">
                    <label for="password" class="form-label">数据库密码</label>
                    <input type="password" class="form-control" id="password" name="db_password" placeholder="请输入数据库密码">
                    <div class="invalid-feedback">请输入数据库密码</div>
                  </div>
                </div>
              </div>

              <div class="admin-setting d-none">
                <div class="bg-light py-2 mb-2 text-center fw-bold">管理员配置</div>
                <div class="row">
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="admin_email" class="form-label">管理员邮箱</label>
                      <input type="text" class="form-control" id="admin_email" name="admin_email" required placeholder="请输入管理员邮箱">
                      <div class="invalid-feedback">请输入管理员邮箱</div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="admin_password" class="form-label">管理员密码</label>
                      <input type="password" class="form-control" id="admin_password" name="admin_password" required placeholder="请输入管理员密码">
                      <div class="invalid-feedback">请输入管理员密码</div>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="d-none">下一步</button>
            </form>
          </div>

          <div class="d-flex justify-content-center mt-4">
            <button type="button" class="btn btn-outline-secondary prev-btn me-3">上一步</button>
            <button type="button" class="btn btn-primary next-btn">下一步</button>
          </div>
        </div>

        <div class="install-4 install-item install-success d-none">
          <div class="head-title">安装完成</div>
          <div class="install-content">
            <div class="icon"><img src="{{ asset('images/install/install-icon-4.svg') }}" class="img-fluid"></div>
            <div class="success-text">
              恭喜您，InnoCMS 安装成功！
            </div>
          </div>
          <div class="d-flex justify-content-center mt-4">
            <a href="{{ front_route('home.index') }}" class="btn btn-primary me-3">访问前台</a>
            <a href="{{ panel_route('panel.index') }}" class="btn btn-primary">访问后台</a>
          </div>
        </div>

        <div class="complete-msg d-none mt-4"></div>
      </div>
    </div>
  </div>

  <script>
    $('.next-btn').click(function() {
      var current = $('.install-item').filter('.active');
      var next = current.next('.install-item');
      if (next.length === 0) {
        return;
      }

      // 第二步
      if (next.hasClass('install-2')) {
        checkStatus();
      }

      // 第三步
      if (next.hasClass('install-3')) {
        $('.install-3 .next-btn').prop('disabled', true);
      }

      if (current.hasClass('install-3')) {
        var form = current.find('form');
        form.removeClass('was-validated');
        if (form[0].checkValidity() === false) {
          form.addClass('was-validated');
          return;
        }

        var data = form.serialize();
        checkComplete(data, function (res) {
          // $('.complete-msg').html('<pre>' + res.message + '</pre>').removeClass('d-none');
          activeStep(current, next);
        })

        return
      }

      activeStep(current, next);
    });

    // 上一步
    $('.prev-btn').click(function() {
      var current = $('.install-item').filter('.active');
      var prev = current.prev('.install-item');
      if (prev.length === 0) {
        return;
      }

      $('.next-btn').prop('disabled', false);
      activeStep(current, prev);
    });

    $('.sql-type').change(function() {
      var type = $(this).val();
      if (type === 'sqlite') {
        $('.mysql-item').find('input').prop('required', false).prop('disabled', true);
      } else {
        $('.mysql-item').find('input').prop('required', true).prop('disabled', false);
      }
    });

    var timer = null;
    // mysql-item 下面的 input 输入
    $('.mysql-item input').on('input', function() {
      var flag = true;
      $('.mysql-item input').each(function() {
        if ($(this).val() === '' && $(this).attr('id') !== 'password') {
          flag = false;
        }
      });

      if (flag) {
        clearTimeout(timer);
        timer = setTimeout(() => {
          checkConnect();
        }, 500);
      }
    });

    function checkConnect() {
      $.ajax({
        url: '/install/connected',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          type: 'mysql',
          db_hostname: $('#host').val(),
          db_port: $('#port').val(),
          db_name: $('#database').val(),
          db_prefix: $('#db_prefix').val(),
          db_username: $('#username').val(),
          db_password: $('#password').val(),
        },
        success: function(res) {
          if (res.db_success) {
            $('.is-invalid').removeClass('is-invalid').next().text('');

            $('.admin-setting').removeClass('d-none');
            $('.next-btn').prop('disabled', false);
            setTimeout(() => {
              $('.install-3 .install-content').animate({scrollTop: $('.install-3 .install-content')[0].scrollHeight}, 400);
            }, 200);
          } else {
            for (var key in res) {
              $('input[name="' + key + '"]').addClass('is-invalid').next().text(res[key]);
            }
          }
        }
      });
    }

    function checkComplete(data, callback) {
      layer.load(2, {shade: [0.3,'#fff'] })
      $.ajax({
        url: '/install/complete',
        type: 'POST',
        data: data,
        success: function(res) {
          if (res.success) {
            callback(res);
          } else {
            alert(res.message);
          }
        },
        complete: function() {
          layer.closeAll('loading');
        }
      });
    }

    function checkStatus() {
      var flag = true;
      $('.install-2 table .bi').each(function() {
        if ($(this).hasClass('text-danger')) {
          flag = false;
        }
      });

      if (!flag) {
        $('.install-2 .next-btn').prop('disabled', true);
      }
    }

    // 激活状态
    function activeStep(current, next) {
      var index = next.index();
      // 删除所有步骤的 active 状态
      $('.progress-wrap li').removeClass('complete active');
      $('.install-wrap .install-item').removeClass('active').addClass('d-none');

      // index 步骤之前的步骤添加 complete 状态
      $('.progress-wrap li').each(function(i) {
        if (i < index) {
          $(this).addClass('complete active');
        }
      });

      $('.progress-wrap li').eq(next.index()).addClass('active');

      $('.install-wrap .install-' + (index + 1)).removeClass('d-none').addClass('active');
    }
  </script>
</body>
</html>