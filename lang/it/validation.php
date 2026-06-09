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
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => 'Il campo :attribute deve essere accettato.',
    'accepted_if'     => 'Il campo :attribute deve essere accettato quando :other è :value.',
    'active_url'      => 'Il campo :attribute deve essere un URL valido.',
    'after'           => 'Il campo :attribute deve essere una data successiva a :date.',
    'after_or_equal'  => 'Il campo :attribute deve essere una data successiva o uguale a :date.',
    'alpha'           => 'Il campo :attribute deve contenere solo lettere.',
    'alpha_dash'      => 'Il campo :attribute deve contenere solo lettere, numeri, trattini e underscore.',
    'alpha_num'       => 'Il campo :attribute deve contenere solo lettere e numeri.',
    'array'           => 'Il campo :attribute deve essere un array.',
    'ascii'           => 'Il campo :attribute deve contenere solo caratteri alfanumerici e simboli a byte singolo.',
    'before'          => 'Il campo :attribute deve essere una data precedente a :date.',
    'before_or_equal' => 'Il campo :attribute deve essere una data precedente o uguale a :date.',
    'between'         => [
        'array'   => 'Il campo :attribute deve avere tra :min e :max elementi.',
        'file'    => 'Il campo :attribute deve essere tra :min e :max kilobyte.',
        'numeric' => 'Il campo :attribute deve essere tra :min e :max.',
        'string'  => 'Il campo :attribute deve essere tra :min e :max caratteri.',
    ],
    'boolean'           => 'Il campo :attribute deve essere vero o falso.',
    'can'               => 'Il campo :attribute contiene un valore non autorizzato.',
    'confirmed'         => 'La conferma del campo :attribute non corrisponde.',
    'current_password'  => 'La password non è corretta.',
    'date'              => 'Il campo :attribute deve essere una data valida.',
    'date_equals'       => 'Il campo :attribute deve essere una data uguale a :date.',
    'date_format'       => 'Il campo :attribute deve corrispondere al formato :format.',
    'decimal'           => 'Il campo :attribute deve avere :decimal cifre decimali.',
    'declined'          => 'Il campo :attribute deve essere rifiutato.',
    'declined_if'       => 'Il campo :attribute deve essere rifiutato quando :other è :value.',
    'different'         => 'Il campo :attribute e :other devono essere diversi.',
    'digits'            => 'Il campo :attribute deve essere di :digits cifre.',
    'digits_between'    => 'Il campo :attribute deve essere tra :min e :max cifre.',
    'dimensions'        => 'Il campo :attribute ha dimensioni immagine non valide.',
    'distinct'          => 'Il campo :attribute ha un valore duplicato.',
    'doesnt_end_with'   => 'Il campo :attribute non deve terminare con uno dei seguenti: :values.',
    'doesnt_start_with' => 'Il campo :attribute non deve iniziare con uno dei seguenti: :values.',
    'email'             => 'Il campo :attribute deve essere un indirizzo email valido.',
    'ends_with'         => 'Il campo :attribute deve terminare con uno dei seguenti: :values.',
    'enum'              => 'Il valore selezionato per :attribute non è valido.',
    'exists'            => 'Il valore selezionato per :attribute non è valido.',
    'extensions'        => 'Il campo :attribute deve avere una delle seguenti estensioni: :values.',
    'file'              => 'Il campo :attribute deve essere un file.',
    'filled'            => 'Il campo :attribute deve avere un valore.',
    'gt'                => [
        'array'   => 'Il campo :attribute deve avere più di :value elementi.',
        'file'    => 'Il campo :attribute deve essere maggiore di :value kilobyte.',
        'numeric' => 'Il campo :attribute deve essere maggiore di :value.',
        'string'  => 'Il campo :attribute deve essere maggiore di :value caratteri.',
    ],
    'gte' => [
        'array'   => 'Il campo :attribute deve avere :value elementi o più.',
        'file'    => 'Il campo :attribute deve essere maggiore o uguale a :value kilobyte.',
        'numeric' => 'Il campo :attribute deve essere maggiore o uguale a :value.',
        'string'  => 'Il campo :attribute deve essere maggiore o uguale a :value caratteri.',
    ],
    'hex_color' => 'Il campo :attribute deve essere un colore esadecimale valido.',
    'image'     => 'Il campo :attribute deve essere un\'immagine.',
    'in'        => 'Il valore selezionato per :attribute non è valido.',
    'in_array'  => 'Il campo :attribute deve esistere in :other.',
    'integer'   => 'Il campo :attribute deve essere un numero intero.',
    'ip'        => 'Il campo :attribute deve essere un indirizzo IP valido.',
    'ipv4'      => 'Il campo :attribute deve essere un indirizzo IPv4 valido.',
    'ipv6'      => 'Il campo :attribute deve essere un indirizzo IPv6 valido.',
    'json'      => 'Il campo :attribute deve essere una stringa JSON valida.',
    'list'      => 'Il campo :attribute deve essere una lista.',
    'lowercase' => 'Il campo :attribute deve essere in minuscolo.',
    'lt'        => [
        'array'   => 'Il campo :attribute deve avere meno di :value elementi.',
        'file'    => 'Il campo :attribute deve essere minore di :value kilobyte.',
        'numeric' => 'Il campo :attribute deve essere minore di :value.',
        'string'  => 'Il campo :attribute deve essere minore di :value caratteri.',
    ],
    'lte' => [
        'array'   => 'Il campo :attribute non deve avere più di :value elementi.',
        'file'    => 'Il campo :attribute deve essere minore o uguale a :value kilobyte.',
        'numeric' => 'Il campo :attribute deve essere minore o uguale a :value.',
        'string'  => 'Il campo :attribute deve essere minore o uguale a :value caratteri.',
    ],
    'mac_address' => 'Il campo :attribute deve essere un indirizzo MAC valido.',
    'max'         => [
        'array'   => 'Il campo :attribute non deve avere più di :max elementi.',
        'file'    => 'Il campo :attribute non deve essere maggiore di :max kilobyte.',
        'numeric' => 'Il campo :attribute non deve essere maggiore di :max.',
        'string'  => 'Il campo :attribute non deve essere maggiore di :max caratteri.',
    ],
    'max_digits' => 'Il campo :attribute non deve avere più di :max cifre.',
    'mimes'      => 'Il campo :attribute deve essere un file di tipo: :values.',
    'mimetypes'  => 'Il campo :attribute deve essere un file di tipo: :values.',
    'min'        => [
        'array'   => 'Il campo :attribute deve avere almeno :min elementi.',
        'file'    => 'Il campo :attribute deve essere almeno :min kilobyte.',
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'string'  => 'Il campo :attribute deve essere almeno :min caratteri.',
    ],
    'min_digits'       => 'Il campo :attribute deve avere almeno :min cifre.',
    'missing'          => 'Il campo :attribute deve essere assente.',
    'missing_if'       => 'Il campo :attribute deve essere assente quando :other è :value.',
    'missing_unless'   => 'Il campo :attribute deve essere assente a meno che :other sia :value.',
    'missing_with'     => 'Il campo :attribute deve essere assente quando :values è presente.',
    'missing_with_all' => 'Il campo :attribute deve essere assente quando :values sono presenti.',
    'multiple_of'      => 'Il campo :attribute deve essere un multiplo di :value.',
    'not_in'           => 'Il valore selezionato per :attribute non è valido.',
    'not_regex'        => 'Il formato del campo :attribute non è valido.',
    'numeric'          => 'Il campo :attribute deve essere un numero.',
    'password'         => [
        'letters'       => 'Il campo :attribute deve contenere almeno una lettera.',
        'mixed'         => 'Il campo :attribute deve contenere almeno una lettera maiuscola e una minuscola.',
        'numbers'       => 'Il campo :attribute deve contenere almeno un numero.',
        'symbols'       => 'Il campo :attribute deve contenere almeno un simbolo.',
        'uncompromised' => 'Il campo :attribute è stato trovato in una fuga di dati. Scegliere un :attribute diverso.',
    ],
    'present'              => 'Il campo :attribute deve essere presente.',
    'present_if'           => 'Il campo :attribute deve essere presente quando :other è :value.',
    'present_unless'       => 'Il campo :attribute deve essere presente a meno che :other sia :value.',
    'present_with'         => 'Il campo :attribute deve essere presente quando :values è presente.',
    'present_with_all'     => 'Il campo :attribute deve essere presente quando :values sono presenti.',
    'prohibited'           => 'Il campo :attribute è vietato.',
    'prohibited_if'        => 'Il campo :attribute è vietato quando :other è :value.',
    'prohibited_unless'    => 'Il campo :attribute è vietato a meno che :other sia in :values.',
    'prohibits'            => 'Il campo :attribute vieta la presenza di :other.',
    'regex'                => 'Il formato del campo :attribute non è valido.',
    'required'             => 'Il campo :attribute è obbligatorio.',
    'required_array_keys'  => 'Il campo :attribute deve contenere voci per: :values.',
    'required_if'          => 'Il campo :attribute è obbligatorio quando :other è :value.',
    'required_if_accepted' => 'Il campo :attribute è obbligatorio quando :other è accettato.',
    'required_if_declined' => 'Il campo :attribute è obbligatorio quando :other è rifiutato.',
    'required_unless'      => 'Il campo :attribute è obbligatorio a meno che :other sia in :values.',
    'required_with'        => 'Il campo :attribute è obbligatorio quando :values è presente.',
    'required_with_all'    => 'Il campo :attribute è obbligatorio quando :values sono presenti.',
    'required_without'     => 'Il campo :attribute è obbligatorio quando :values non è presente.',
    'required_without_all' => 'Il campo :attribute è obbligatorio quando nessuno di :values è presente.',
    'same'                 => 'Il campo :attribute deve corrispondere a :other.',
    'size'                 => [
        'array'   => 'Il campo :attribute deve contenere :size elementi.',
        'file'    => 'Il campo :attribute deve essere :size kilobyte.',
        'numeric' => 'Il campo :attribute deve essere :size.',
        'string'  => 'Il campo :attribute deve essere :size caratteri.',
    ],
    'starts_with' => 'Il campo :attribute deve iniziare con uno dei seguenti: :values.',
    'string'      => 'Il campo :attribute deve essere una stringa.',
    'timezone'    => 'Il campo :attribute deve essere un fuso orario valido.',
    'unique'      => 'Il valore di :attribute è già stato utilizzato.',
    'uploaded'    => 'Il caricamento di :attribute non è riuscito.',
    'uppercase'   => 'Il campo :attribute deve essere in maiuscolo.',
    'url'         => 'Il campo :attribute deve essere un URL valido.',
    'ulid'        => 'Il campo :attribute deve essere un ULID valido.',
    'uuid'        => 'Il campo :attribute deve essere un UUID valido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
