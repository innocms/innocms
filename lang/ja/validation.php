<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、バリデータークラスで使用されるデフォルトのエラーメッセージです。
    | 一部のルールにはサイズルールのように複数のバージョンがあります。
    | ここで各メッセージを自由に調整してください。
    |
    */

    'accepted'        => ':attributeを承認してください。',
    'accepted_if'     => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url'      => ':attributeは有効なURLを指定してください。',
    'after'           => ':attributeは:dateより後の日付を指定してください。',
    'after_or_equal'  => ':attributeは:date以降の日付を指定してください。',
    'alpha'           => ':attributeは文字のみを指定してください。',
    'alpha_dash'      => ':attributeは文字、数字、ハイフン、アンダースコアのみを指定してください。',
    'alpha_num'       => ':attributeは文字と数字のみを指定してください。',
    'array'           => ':attributeは配列を指定してください。',
    'ascii'           => ':attributeはシングルバイトの英数字と記号のみを指定してください。',
    'before'          => ':attributeは:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeは:date以前の日付を指定してください。',
    'between'         => [
        'array'   => ':attributeは:min〜:max個の要素を指定してください。',
        'file'    => ':attributeは:min〜:maxキロバイトのファイルを指定してください。',
        'numeric' => ':attributeは:min〜:maxの間の値を指定してください。',
        'string'  => ':attributeは:min〜:max文字で指定してください。',
    ],
    'boolean'           => ':attributeはtrueまたはfalseを指定してください。',
    'can'               => ':attributeに許可されていない値が含まれています。',
    'confirmed'         => ':attributeと確認用の入力が一致しません。',
    'current_password'  => 'パスワードが正しくありません。',
    'date'              => ':attributeは有効な日付を指定してください。',
    'date_equals'       => ':attributeは:dateと同じ日付を指定してください。',
    'date_format'       => ':attributeは:format形式を指定してください。',
    'decimal'           => ':attributeは:decimal桁の小数を指定してください。',
    'declined'          => ':attributeは拒否してください。',
    'declined_if'       => ':otherが:valueの場合、:attributeは拒否してください。',
    'different'         => ':attributeと:otherは異なる値を指定してください。',
    'digits'            => ':attributeは:digits桁の数字を指定してください。',
    'digits_between'    => ':attributeは:min〜:max桁の数字を指定してください。',
    'dimensions'        => ':attributeの画像サイズが無効です。',
    'distinct'          => ':attributeに重複した値があります。',
    'doesnt_end_with'   => ':attributeは次のいずれかで終わってはいけません：:values。',
    'doesnt_start_with' => ':attributeは次のいずれかで始まってはいけません：:values。',
    'email'             => ':attributeは有効なメールアドレスを指定してください。',
    'ends_with'         => ':attributeは次のいずれかで終わる値を指定してください：:values。',
    'enum'              => '選択された:attributeは無効です。',
    'exists'            => '選択された:attributeは無効です。',
    'extensions'        => ':attributeは次のいずれかの拡張子を持つファイルを指定してください：:values。',
    'file'              => ':attributeはファイルを指定してください。',
    'filled'            => ':attributeは値を入力してください。',
    'gt'                => [
        'array'   => ':attributeは:value個より多い要素を指定してください。',
        'file'    => ':attributeは:valueキロバイトより大きいファイルを指定してください。',
        'numeric' => ':attributeは:valueより大きい値を指定してください。',
        'string'  => ':attributeは:value文字より多く指定してください。',
    ],
    'gte' => [
        'array'   => ':attributeは:value個以上の要素を指定してください。',
        'file'    => ':attributeは:valueキロバイト以上のファイルを指定してください。',
        'numeric' => ':attributeは:value以上の値を指定してください。',
        'string'  => ':attributeは:value文字以上で指定してください。',
    ],
    'hex_color' => ':attributeは有効な16進数カラーコードを指定してください。',
    'image'     => ':attributeは画像を指定してください。',
    'in'        => '選択された:attributeは無効です。',
    'in_array'  => ':attributeは:otherに存在する値を指定してください。',
    'integer'   => ':attributeは整数を指定してください。',
    'ip'        => ':attributeは有効なIPアドレスを指定してください。',
    'ipv4'      => ':attributeは有効なIPv4アドレスを指定してください。',
    'ipv6'      => ':attributeは有効なIPv6アドレスを指定してください。',
    'json'      => ':attributeは有効なJSON文字列を指定してください。',
    'list'      => ':attributeはリストを指定してください。',
    'lowercase' => ':attributeは小文字で指定してください。',
    'lt'        => [
        'array'   => ':attributeは:value個より少ない要素を指定してください。',
        'file'    => ':attributeは:valueキロバイトより小さいファイルを指定してください。',
        'numeric' => ':attributeは:valueより小さい値を指定してください。',
        'string'  => ':attributeは:value文字より少なく指定してください。',
    ],
    'lte' => [
        'array'   => ':attributeは:value個以下の要素を指定してください。',
        'file'    => ':attributeは:valueキロバイト以下のファイルを指定してください。',
        'numeric' => ':attributeは:value以下の値を指定してください。',
        'string'  => ':attributeは:value文字以下で指定してください。',
    ],
    'mac_address' => ':attributeは有効なMACアドレスを指定してください。',
    'max'         => [
        'array'   => ':attributeは:max個以下の要素を指定してください。',
        'file'    => ':attributeは:maxキロバイト以下のファイルを指定してください。',
        'numeric' => ':attributeは:max以下の値を指定してください。',
        'string'  => ':attributeは:max文字以下で指定してください。',
    ],
    'max_digits' => ':attributeは:max桁以下の数字を指定してください。',
    'mimes'      => ':attributeは:valuesタイプのファイルを指定してください。',
    'mimetypes'  => ':attributeは:valuesタイプのファイルを指定してください。',
    'min'        => [
        'array'   => ':attributeは:min個以上の要素を指定してください。',
        'file'    => ':attributeは:minキロバイト以上のファイルを指定してください。',
        'numeric' => ':attributeは:min以上の値を指定してください。',
        'string'  => ':attributeは:min文字以上で指定してください。',
    ],
    'min_digits'       => ':attributeは:min桁以上の数字を指定してください。',
    'missing'          => ':attributeは存在しないようにしてください。',
    'missing_if'       => ':otherが:valueの場合、:attributeは存在しないようにしてください。',
    'missing_unless'   => ':otherが:valueでない限り、:attributeは存在しないようにしてください。',
    'missing_with'     => ':valuesが存在する場合、:attributeは存在しないようにしてください。',
    'missing_with_all' => ':valuesがすべて存在する場合、:attributeは存在しないようにしてください。',
    'multiple_of'      => ':attributeは:valueの倍数を指定してください。',
    'not_in'           => '選択された:attributeは無効です。',
    'not_regex'        => ':attributeの形式が無効です。',
    'numeric'          => ':attributeは数値を指定してください。',
    'password'         => [
        'letters'       => ':attributeは少なくとも1つの文字を含めてください。',
        'mixed'         => ':attributeは少なくとも1つの大文字と1つの小文字を含めてください。',
        'numbers'       => ':attributeは少なくとも1つの数字を含めてください。',
        'symbols'       => ':attributeは少なくとも1つの記号を含めてください。',
        'uncompromised' => '指定された:attributeはデータ漏洩で確認されています。別の:attributeを選択してください。',
    ],
    'present'              => ':attributeは存在するようにしてください。',
    'present_if'           => ':otherが:valueの場合、:attributeは存在するようにしてください。',
    'present_unless'       => ':otherが:valueでない限り、:attributeは存在するようにしてください。',
    'present_with'         => ':valuesが存在する場合、:attributeは存在するようにしてください。',
    'present_with_all'     => ':valuesがすべて存在する場合、:attributeは存在するようにしてください。',
    'prohibited'           => ':attributeは禁止されています。',
    'prohibited_if'        => ':otherが:valueの場合、:attributeは禁止されています。',
    'prohibited_unless'    => ':otherが:valuesに含まれない限り、:attributeは禁止されています。',
    'prohibits'            => ':attributeは:otherの存在を禁止しています。',
    'regex'                => ':attributeの形式が無効です。',
    'required'             => ':attributeは必須です。',
    'required_array_keys'  => ':attributeは次のエントリを含めてください：:values。',
    'required_if'          => ':otherが:valueの場合、:attributeは必須です。',
    'required_if_accepted' => ':otherが承認された場合、:attributeは必須です。',
    'required_if_declined' => ':otherが拒否された場合、:attributeは必須です。',
    'required_unless'      => ':otherが:valuesに含まれない限り、:attributeは必須です。',
    'required_with'        => ':valuesが存在する場合、:attributeは必須です。',
    'required_with_all'    => ':valuesがすべて存在する場合、:attributeは必須です。',
    'required_without'     => ':valuesが存在しない場合、:attributeは必須です。',
    'required_without_all' => ':valuesがすべて存在しない場合、:attributeは必須です。',
    'same'                 => ':attributeは:otherと一致してください。',
    'size'                 => [
        'array'   => ':attributeは:size個の要素を指定してください。',
        'file'    => ':attributeは:sizeキロバイトのファイルを指定してください。',
        'numeric' => ':attributeは:sizeを指定してください。',
        'string'  => ':attributeは:size文字で指定してください。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる値を指定してください：:values。',
    'string'      => ':attributeは文字列を指定してください。',
    'timezone'    => ':attributeは有効なタイムゾーンを指定してください。',
    'unique'      => ':attributeは既に使用されています。',
    'uploaded'    => ':attributeのアップロードに失敗しました。',
    'uppercase'   => ':attributeは大文字で指定してください。',
    'url'         => ':attributeは有効なURLを指定してください。',
    'ulid'        => ':attributeは有効なULIDを指定してください。',
    'uuid'        => ':attributeは有効なUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション言語行
    |--------------------------------------------------------------------------
    |
    | ここでは、「attribute.rule」の規約を使用して、属性のカスタムバリデーション
    | メッセージを指定できます。これにより、特定の属性ルールに対してカスタム
    | 言語行を素早く指定できます。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、属性プレースホルダーを「email」の代わりに
    | 「メールアドレス」のように読みやすいものに置き換えるために使用されます。
    | これによりメッセージをよりわかりやすくできます。
    |
    */

    'attributes' => [],

];
