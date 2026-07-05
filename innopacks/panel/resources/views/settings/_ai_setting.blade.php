<!-- AI Settings -->
<div class="tab-pane fade" id="tab-setting-ai">
  <div class="alert alert-info small mb-3">
    配置 AI 服务后,SitePilot 等功能可调用 LLM 自动生成内容。API Key 仅保存在本系统,不会上传任何第三方。默认 Driver 为生成时实际使用的服务,需在下方启用并填写对应 Key。
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">默认 Driver</h5>
    </div>
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label class="form-label">默认 AI 服务</label>
          <select class="form-select" name="ai_model">
            @php $currentDefault = system_setting('ai_model', 'glm'); @endphp
            @foreach (['glm' => 'GLM 智谱', 'openai' => 'OpenAI', 'deepseek' => 'DeepSeek', 'kimi' => 'Kimi Moonshot', 'doubao' => '火山豆包', 'qianwen' => '通义千问', 'hunyuan' => '腾讯混元', 'minimax' => 'MiniMax', 'anthropic' => 'Anthropic Claude'] as $code => $label)
              <option value="{{ $code }}" @selected($currentDefault === $code)>{{ $label }}</option>
            @endforeach
          </select>
          <div class="form-text">SitePilot / 内容生成等场景默认使用此 Driver,需下方对应 Provider 已启用且填了 Key。</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Provider 配置</h5>
    </div>
    <div class="card-body">
      @php
        $providers = [
          'glm'        => ['label' => 'GLM 智谱',     'base_url' => 'https://open.bigmodel.cn/api/paas/v4',          'model_hint' => 'glm-4 / glm-4-plus / glm-4-flash'],
          'openai'     => ['label' => 'OpenAI',       'base_url' => 'https://api.openai.com',                        'model_hint' => 'gpt-4o / gpt-4o-mini'],
          'deepseek'   => ['label' => 'DeepSeek',     'base_url' => 'https://api.deepseek.com/v1',                   'model_hint' => 'deepseek-chat / deepseek-coder'],
          'kimi'       => ['label' => 'Kimi (Moonshot)', 'base_url' => 'https://api.moonshot.cn',                    'model_hint' => 'moonshot-v1-8k / moonshot-v1-32k'],
          'doubao'     => ['label' => '火山豆包',      'base_url' => 'https://ark.cn-beijing.volces.com/api/v3',      'model_hint' => '填 Endpoint ID,如 ep-xxxxx'],
          'qianwen'    => ['label' => '通义千问',      'base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1', 'model_hint' => 'qwen-plus / qwen-turbo / qwen-max'],
          'hunyuan'    => ['label' => '腾讯混元',      'base_url' => 'https://api.hunyuan.cloud.tencent.com/v1',      'model_hint' => 'hunyuan-turbo / hunyuan-pro'],
          'minimax'    => ['label' => 'MiniMax',      'base_url' => 'https://api.minimax.chat/v1',                   'model_hint' => 'abab6.5s-chat / abab6.5-chat'],
          'anthropic'  => ['label' => 'Anthropic Claude', 'base_url' => 'https://api.anthropic.com',                 'model_hint' => 'claude-3-5-sonnet-latest / claude-3-opus'],
        ];
      @endphp

      <div class="row g-3">
        @foreach ($providers as $code => $info)
          @php
            $enabled  = (bool) system_setting($code . '_enabled', 0);
            $apiKey   = system_setting($code . '_api_key', '');
            $baseUrl  = system_setting($code . '_base_url', $info['base_url']);
            $model    = system_setting($code . '_model', '');
          @endphp
          <div class="col-md-6">
            <div class="card border-{{ $enabled ? 'success' : 'secondary' }} h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0">{{ $info['label'] }}</h6>
                  <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" data-ai-switch="{{ $code }}" @checked($enabled)>
                    <input type="hidden" name="{{ $code }}_enabled" value="{{ $enabled ? 1 : 0 }}">
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label small text-muted mb-1">API Key</label>
                  <input type="password" class="form-control form-control-sm" name="{{ $code }}_api_key" value="{{ $apiKey }}" placeholder="{{ $apiKey ? '已配置 (重新输入可覆盖)' : 'sk-...' }}" autocomplete="new-password">
                </div>
                <div class="mb-2">
                  <label class="form-label small text-muted mb-1">Base URL</label>
                  <input type="text" class="form-control form-control-sm" name="{{ $code }}_base_url" value="{{ $baseUrl }}">
                </div>
                <div>
                  <label class="form-label small text-muted mb-1">Model</label>
                  <input type="text" class="form-control form-control-sm" name="{{ $code }}_model" value="{{ $model }}" placeholder="{{ $info['model_hint'] }}">
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

@pushOnce('footer')
  <script>
    $(function () {
      $(document).on('change', 'input[data-ai-switch]', function () {
        $(this).next('input[type=hidden]').val(this.checked ? 1 : 0);
      });

      // 提交前移除空的 api_key 字段,避免覆盖已存的真实 key
      $('#app-form').on('submit', function () {
        $(this).find('input[name$="_api_key"]').each(function () {
          if (!this.value) this.disabled = true;
        });
      });
    });
  </script>
@endPushOnce
