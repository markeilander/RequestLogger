# Laravel 5 Request Logger package

Log all request and responses


### Table of contents
 
[TOC]

## Usage

### Step 1: Add the Service Provider

In your `config/app.php` add `Eilander\RequestLogger\Providers\RequestLoggerServiceProvider:class` to the end of the `providers` array:


```
<?php
'providers' => [
    ...
    Eilander\RequestLogger\Providers\RequestLoggerServiceProvider::class,
],

```