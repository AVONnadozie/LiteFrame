# LiteFrame: A PHP Micro Framework
[![Latest Stable Version](https://poser.pugx.org/avonnadozie/liteframe/v/stable)](https://packagist.org/packages/avonnadozie/liteframe)
[![Latest Unstable Version](https://poser.pugx.org/avonnadozie/liteframe/v/unstable)](https://packagist.org/packages/avonnadozie/liteframe)
[![Build Status](https://travis-ci.org/AVONnadozie/LiteFrame.svg?branch=master)](https://travis-ci.org/AVONnadozie/LiteFrame)
[![License](https://poser.pugx.org/avonnadozie/liteframe/license)](https://packagist.org/packages/avonnadozie/liteframe)

Frameworks are great but most are wild; difficult to setup, consume lots of resources, require advanced knowledge 
of programming or knowledge of everything in some 100 page docs.

Although they have their benefits, it's a lot for a beginner, someone who is new to frameworks or someone simply behind schedule.

A typical setup process on a standard framework looks like this
 - Install composer (If it does not exists)
 - Run composer install
 - Generate app key
 - Configure env
 - Setup and run migration files
 - Configure server document root (I'm sorry if you're on a shared hosting, good luck hacking your way through)
 - And the list continues

LiteFrame is a small (micro) but powerful framework that selectively re-implements basic features of standard frameworks 
in non heartbreaking ways.

## Features
- [x] Fast and lightweight
- [x] Easy setup (no shell commands required)
- [x] RedBeanPHP - automatically builds your database on the fly. (No migration files required)
- [x] Easy Routing
- [x] Middleware Support
- [x] Commands
- [x] Job Scheduling Support
- [x] Support for Blade Templating using [BladeOne](https://github.com/EFTEC/BladeOne)
- [ ] FlySystem Support (for files)
- [ ] Request and Data Validation
- [ ] Modularity
- [x] Unit Testing Support

## Installation
Download the latest release [here](https://github.com/AVONnadozie/LiteFrame/releases) and unzip it. that's all!

Still need it the Composer way?

```bash
composer create-project avonnadozie/liteframe
```

## Documentation
* Locally in [docs folder](./docs)
* [Online documentation](https://avonnadozie.github.io/LiteFrame/)

## Feedback
For bugs, improvements or guide, simply create an [issue](https://github.com/AVONnadozie/LiteFrame/issues). Thanks 👍

## How to Contribute
* Fork the project.
* Make your bug fix or feature addition.
* Add tests for it. This is important so we don't break it in a future version unintentionally.
* Send a pull request.
