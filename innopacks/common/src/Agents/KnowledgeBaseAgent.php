<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;

class KnowledgeBaseAgent implements Agent, Conversational
{
    use Promptable;

    public function __construct(
        private readonly array $messages = [],
        private readonly ?string $context = null,
    ) {}

    public function instructions(): \Stringable|string
    {
        $base = 'You are a knowledgeable assistant. Answer questions based on the provided context. '.
                'If the answer is not in the context, say so honestly.';

        if ($this->context) {
            $base .= "\n\nContext:\n".$this->context;
        }

        return $base;
    }

    public function messages(): iterable
    {
        return array_map(
            fn (array $msg) => new Message($msg['role'], $msg['content']),
            $this->messages
        );
    }
}
