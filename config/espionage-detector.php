<?php

return [
    'esi_scopes' => [
        'required' => [
            'esi-wallet.read_character_wallet.v1',
            'esi-mail.read_mail.v1',
            'esi-contracts.read_character_contracts.v1',
            'esi-characters.read_contacts.v1'
        ],
        'optional' => []
    ],
    
    'thresholds' => [
        'warning' => 1,
        'critical' => 3
    ],
    
    'data_retention' => 30, // days
];