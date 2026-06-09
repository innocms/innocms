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
    | Lignes de traduction pour la validation
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes contiennent les messages d'erreur par défaut
    | utilisés par la classe de validation. Certaines de ces règles ont plusieurs
    | versions telles que les règles de taille. N'hésitez pas à ajuster ces messages.
    |
    */

    'accepted'        => 'Le champ :attribute doit être accepté.',
    'accepted_if'     => 'Le champ :attribute doit être accepté quand :other est :value.',
    'active_url'      => 'Le champ :attribute doit être une URL valide.',
    'after'           => 'Le champ :attribute doit être une date postérieure à :date.',
    'after_or_equal'  => 'Le champ :attribute doit être une date postérieure ou égale à :date.',
    'alpha'           => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash'      => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres, des tirets et des underscores.',
    'alpha_num'       => 'Le champ :attribute doit contenir uniquement des lettres et des chiffres.',
    'array'           => 'Le champ :attribute doit être un tableau.',
    'ascii'           => 'Le champ :attribute doit contenir uniquement des caractères alphanumériques et symboles mono-octet.',
    'before'          => 'Le champ :attribute doit être une date antérieure à :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale à :date.',
    'between'         => [
        'array'   => 'Le champ :attribute doit contenir entre :min et :max éléments.',
        'file'    => 'Le champ :attribute doit être compris entre :min et :max kilo-octets.',
        'numeric' => 'Le champ :attribute doit être compris entre :min et :max.',
        'string'  => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean'           => 'Le champ :attribute doit être vrai ou faux.',
    'can'               => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed'         => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password'  => 'Le mot de passe est incorrect.',
    'date'              => 'Le champ :attribute doit être une date valide.',
    'date_equals'       => 'Le champ :attribute doit être une date égale à :date.',
    'date_format'       => 'Le champ :attribute doit correspondre au format :format.',
    'decimal'           => 'Le champ :attribute doit avoir :decimal décimales.',
    'declined'          => 'Le champ :attribute doit être refusé.',
    'declined_if'       => 'Le champ :attribute doit être refusé quand :other est :value.',
    'different'         => 'Le champ :attribute et :other doivent être différents.',
    'digits'            => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between'    => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions'        => 'Le champ :attribute a des dimensions d\'image invalides.',
    'distinct'          => 'Le champ :attribute a une valeur en double.',
    'doesnt_end_with'   => 'Le champ :attribute ne doit pas se terminer par l\'un des éléments suivants : :values.',
    'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par l\'un des éléments suivants : :values.',
    'email'             => 'Le champ :attribute doit être une adresse e-mail valide.',
    'ends_with'         => 'Le champ :attribute doit se terminer par l\'un des éléments suivants : :values.',
    'enum'              => 'Le champ :attribute sélectionné est invalide.',
    'exists'            => 'Le champ :attribute sélectionné est invalide.',
    'extensions'        => 'Le champ :attribute doit avoir l\'une des extensions suivantes : :values.',
    'file'              => 'Le champ :attribute doit être un fichier.',
    'filled'            => 'Le champ :attribute doit avoir une valeur.',
    'gt'                => [
        'array'   => 'Le champ :attribute doit contenir plus de :value éléments.',
        'file'    => 'Le champ :attribute doit être supérieur à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
        'string'  => 'Le champ :attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array'   => 'Le champ :attribute doit contenir :value éléments ou plus.',
        'file'    => 'Le champ :attribute doit être supérieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'string'  => 'Le champ :attribute doit contenir :value caractères ou plus.',
    ],
    'hex_color' => 'Le champ :attribute doit être une couleur hexadécimale valide.',
    'image'     => 'Le champ :attribute doit être une image.',
    'in'        => 'Le champ :attribute sélectionné est invalide.',
    'in_array'  => 'Le champ :attribute doit exister dans :other.',
    'integer'   => 'Le champ :attribute doit être un entier.',
    'ip'        => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4'      => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6'      => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json'      => 'Le champ :attribute doit être une chaîne JSON valide.',
    'list'      => 'Le champ :attribute doit être une liste.',
    'lowercase' => 'Le champ :attribute doit être en minuscules.',
    'lt'        => [
        'array'   => 'Le champ :attribute doit contenir moins de :value éléments.',
        'file'    => 'Le champ :attribute doit être inférieur à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur à :value.',
        'string'  => 'Le champ :attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array'   => 'Le champ :attribute ne doit pas contenir plus de :value éléments.',
        'file'    => 'Le champ :attribute doit être inférieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'string'  => 'Le champ :attribute doit contenir :value caractères ou moins.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max'         => [
        'array'   => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
        'file'    => 'Le champ :attribute ne doit pas dépasser :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
        'string'  => 'Le champ :attribute ne doit pas contenir plus de :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute ne doit pas contenir plus de :max chiffres.',
    'mimes'      => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes'  => 'Le champ :attribute doit être un fichier de type : :values.',
    'min'        => [
        'array'   => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file'    => 'Le champ :attribute doit faire au moins :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string'  => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'min_digits'       => 'Le champ :attribute doit contenir au moins :min chiffres.',
    'missing'          => 'Le champ :attribute doit être absent.',
    'missing_if'       => 'Le champ :attribute doit être absent quand :other est :value.',
    'missing_unless'   => 'Le champ :attribute doit être absent sauf si :other est :value.',
    'missing_with'     => 'Le champ :attribute doit être absent quand :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être absent quand :values sont présents.',
    'multiple_of'      => 'Le champ :attribute doit être un multiple de :value.',
    'not_in'           => 'Le champ :attribute sélectionné est invalide.',
    'not_regex'        => 'Le format du champ :attribute est invalide.',
    'numeric'          => 'Le champ :attribute doit être un nombre.',
    'password'         => [
        'letters'       => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed'         => 'Le champ :attribute doit contenir au moins une lettre majuscule et une lettre minuscule.',
        'numbers'       => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols'       => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le champ :attribute est apparu dans une fuite de données. Veuillez choisir un autre :attribute.',
    ],
    'present'              => 'Le champ :attribute doit être présent.',
    'present_if'           => 'Le champ :attribute doit être présent quand :other est :value.',
    'present_unless'       => 'Le champ :attribute doit être présent sauf si :other est :value.',
    'present_with'         => 'Le champ :attribute doit être présent quand :values est présent.',
    'present_with_all'     => 'Le champ :attribute doit être présent quand :values sont présents.',
    'prohibited'           => 'Le champ :attribute est interdit.',
    'prohibited_if'        => 'Le champ :attribute est interdit quand :other est :value.',
    'prohibited_unless'    => 'Le champ :attribute est interdit sauf si :other figure dans :values.',
    'prohibits'            => 'Le champ :attribute interdit la présence de :other.',
    'regex'                => 'Le format du champ :attribute est invalide.',
    'required'             => 'Le champ :attribute est obligatoire.',
    'required_array_keys'  => 'Le champ :attribute doit contenir des entrées pour : :values.',
    'required_if'          => 'Le champ :attribute est obligatoire quand :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire quand :other est accepté.',
    'required_if_declined' => 'Le champ :attribute est obligatoire quand :other est refusé.',
    'required_unless'      => 'Le champ :attribute est obligatoire sauf si :other figure dans :values.',
    'required_with'        => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_with_all'    => 'Le champ :attribute est obligatoire quand :values sont présents.',
    'required_without'     => 'Le champ :attribute est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire quand aucun de :values n\'est présent.',
    'same'                 => 'Le champ :attribute doit correspondre à :other.',
    'size'                 => [
        'array'   => 'Le champ :attribute doit contenir :size éléments.',
        'file'    => 'Le champ :attribute doit faire :size kilo-octets.',
        'numeric' => 'Le champ :attribute doit être :size.',
        'string'  => 'Le champ :attribute doit contenir :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par l\'un des éléments suivants : :values.',
    'string'      => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone'    => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique'      => 'Le champ :attribute a déjà été pris.',
    'uploaded'    => 'Le téléversement du champ :attribute a échoué.',
    'uppercase'   => 'Le champ :attribute doit être en majuscules.',
    'url'         => 'Le champ :attribute doit être une URL valide.',
    'ulid'        => 'Le champ :attribute doit être un ULID valide.',
    'uuid'        => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Lignes de traduction de validation personnalisées
    |--------------------------------------------------------------------------
    |
    | Ici vous pouvez spécifier des messages de validation personnalisés pour les
    | attributs en utilisant la convention "attribut.règle" pour nommer les lignes.
    | Cela permet de spécifier rapidement un message personnalisé pour une règle donnée.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs de validation personnalisés
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes sont utilisées pour remplacer notre espace réservé
    | d'attribut par quelque chose de plus lisible tel que « Adresse e-mail » au lieu
    | de « email ». Cela nous aide simplement à rendre nos messages plus expressifs.
    |
    */

    'attributes' => [],

];
