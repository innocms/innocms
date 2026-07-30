<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Aicore\Agents;

use InnoCMS\Aicore\Services\ToolRegistry;
use InnoCMS\Aicore\Tools\LaravelAiToolAdapter;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;

#[MaxSteps(10)]
class PanelChatAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    /** @var Message[] */
    private array $conversationMessages;

    /**
     * @param  Message[]  $messages  Conversation history from the frontend
     */
    public function __construct(array $messages = [])
    {
        $this->conversationMessages = $messages;
    }

    public function instructions(): \Stringable|string
    {
        $tools = $this->formatToolsForPrompt();

        return <<<PROMPT
你是一个专业的 CMS 站点 AI 助手，服务于 InnoCMS 后台管理面板。

## 角色
- 你是站点内容运营的智能助手，帮助管理员查询内容、管理文章、执行操作
- 回答简洁专业，用中文
- 当被问到数据相关问题时，优先使用工具查询真实数据，而不是猜测

## 能力
你可以访问以下工具来查询和管理站点内容：

{$tools}

## 规则
- 查询数据时始终使用工具获取最新数据
- 日期使用 YYYY-MM-DD 格式
- 如果用户的问题需要调用多个工具，依次调用
- 将工具返回的 JSON 数据转化为自然语言回答

## 输出格式（重要）
- 回答统一使用中文，数据展示清晰专业
- **数据表格**：多条数据用 Markdown 表格，列对齐
- **小标题**：用 ### 分段，每个数据维度独立一节
- **列表**：建议等用无序列表，每条一行
- 简洁优先，不要长篇大论，运营人员需要快速扫读
PROMPT;
    }

    public function messages(): iterable
    {
        return $this->conversationMessages;
    }

    public function tools(): iterable
    {
        $registry = app(ToolRegistry::class);

        return array_map(
            fn ($tool) => new LaravelAiToolAdapter($tool),
            array_values($registry->all())
        );
    }

    private function formatToolsForPrompt(): string
    {
        $registry = app(ToolRegistry::class);
        $lines    = [];

        foreach ($registry->all() as $tool) {
            $lines[] = "- **{$tool->name()}**: {$tool->description()}";
        }

        return implode("\n", $lines);
    }
}
