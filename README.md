# LiteFrame: A PHP Micro Framework
[![Latest Stable Version](https://poser.pugx.org/avonnadozie/liteframe/v/stable)](https://packagist.org/packages/avonnadozie/liteframe)
[![Latest Unstable Version](https://poser.pugx.org/avonnadozie/liteframe/v/unstable)](https://packagist.org/packages/avonnadozie/liteframe)
[![Build Status](https://travis-ci.org/AVONnadozie/LiteFrame.svg?branch=master)](https://travis-ci.org/AVONnadozie/LiteFrame)
[![License](https://poser.pugx.org/avonnadozie/liteframe/license)](https://packagist.org/packages/avonnadozie/liteframe)

So many amazing PHP frameworks out there but somehow we still wanted **something light**, fast, easy to setup with no requirement for shell/commands and still has the functionalities of a modern MVC framework; something shared hosting users will clap for.

LiteFrame is a lightweight PHP framework designed to earn the claps.

## Target Features
- [x] Fast
- [x] Easy setup (no shell commands required)
- [x] Routing
- [x] Middleware
- [ ] Filtering and Validation
- [ ] Security
- [x] Job Scheduling
- [x] Error Logging
- [x] Testing
- [ ] Dependency Injection
- [ ] Modular

## Requirements
PHP 5.5 and above

## Installation
Download the latest release [here](https://github.com/AVONnadozie/LiteFrame/releases) and unzip it. that's all!

Still need it the Composer way?

```bash
composer create-project avonnadozie/liteframe
```

Remember to set `components/logs` to be write-able, preferably `755`

## Documentation
* [Online documentation](https://avonnadozie.github.io/LiteFrame/) (Recommended)
* [Download as pdf](#) (Not currently available and not always up to date)

## Feedback
For bugs, improvements or guide, simply create an [issue](https://github.com/AVONnadozie/LiteFrame/issues). Thanks 👍

## How to Contribute
* Fork the project.
* Make your bug fix or feature addition.
* Add tests for it. This is important so we don't break it in a future version unintentionally.
* Send a pull request.

## Coding Guidelines
Use [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to (re)format your sourcecode for compliance with this project's coding guidelines:

```bash
$ wget http://get.sensiolabs.org/php-cs-fixer.phar

$ php php-cs-fixer.phar fix <dir>
```
