<?php
return array(

	/*
    |--------------------------------------------------------------------------
    | Log
    |--------------------------------------------------------------------------
    |
    | Enables: true/false
    | Format: define formats you want to use
    | Available placeholder:
    | {method}: The method used (GET/POST/PUT/DELETE)
    | {time}: Time in ms
    | {status}: status code (eg: 200, 404, etc)
    | {body}: The body send with the request
    | {full-url}: The full url that is called
    | {content}: The content that is returned bij the request
    */
	'log' => [
		'enabled' => env('LOG_REQUESTS', false),
		'format'  => [
			'full' 	  => '{method} in {time} [{status}] {full-url} {content}',
			'minimal' => '{method} in {time} [{status}] {full-url}'
		]
	],

	/*
    |--------------------------------------------------------------------------
    | Formats
    |--------------------------------------------------------------------------
    |
    | Available options: default, except
    |
    | Default: the default format for all requests
    | Except: the format used for the exception url's, leave empty to skip logging
    */
	'format' => [
		'default' => 'full',
		'except'   => 'minimal'
	],

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    |
    */
    'methods' => [
        'POST', 'GET', 'PUT', 'DELETE'
    ],

    /*
    |--------------------------------------------------------------------------
    | Only
    |--------------------------------------------------------------------------
    |
    | Only log request that start with
    */
    'only' => [
	   'api/*'
    ],

	/*
    |--------------------------------------------------------------------------
    | Except
    |--------------------------------------------------------------------------
    |
    | The url's that wil not be logged
    */
    'except' => [

	],

    /*
    |--------------------------------------------------------------------------
    | Minimal
    |--------------------------------------------------------------------------
    |
    | The url's that wil be handled by the except formatter
    */
    'except-format' => [

	],
);