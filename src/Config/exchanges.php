<?php

return [
    'zebpay' => [
        'bitcoin' => [
            'exchange' => 'zebpay',
            'coin' => 'bitcoin',
            'url' => 'https://www.zebapi.com/api/v1/market/ticker/btc/inr', // Removed compress.zlib wrapper as Guzzle handles it or we can handle it
            'datatype' => 'json',
            'api_wait' => 120,
            'buy_key' => 'buy',
            'sell_key' => 'sell'
        ]
    ],
    'coinome' => [
        'bitcoin' => [
            'exchange' => 'coinome',
            'coin' => 'bitcoin',
            'url' => 'https://www.coinome.com/api/v1/ticker.json',
            'datatype' => 'json',
            'api_wait' => 120,
            'buy_key' => 'BTC-INR||lowest_ask',
            'sell_key' => 'BTC-INR||highest_bid'
        ],
        'bitcash' => [
            'exchange' => 'coinome',
            'coin' => 'bitcash',
            'url' => 'https://www.coinome.com/api/v1/ticker.json',
            'datatype' => 'json',
            'api_wait' => 120,
            'buy_key' => 'BCH-INR||lowest_ask',
            'sell_key' => 'BCH-INR||highest_bid'
        ],
        'litecoin' => [
            'exchange' => 'coinome',
            'coin' => 'litecoin',
            'url' => 'https://www.coinome.com/api/v1/ticker.json',
            'datatype' => 'json',
            'api_wait' => 120,
            'buy_key' => 'LTC-INR||lowest_ask',
            'sell_key' => 'LTC-INR||highest_bid'
        ],
    ]
];
