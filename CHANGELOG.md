# LiteFrame Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [Unreleased]

- Modularity/ Support for packages
- Use one namespace App\\ for application controllers, models, middlewares and commands 

## [0.3.0] - 2018-09-14
### Added

- LiteFrame now uses liteframe/liteframe-core

### Changed

- LiteFrame\Http\Request now inherits Symfony\Component\HttpFoundation\Request
- composer files

### Fixed

- Issues with session

### Removed

- core folder now exists as a separate package


## [0.2.0] - 2018-08-17
### Added
- RedBeanPHP support for database
- Route Grouping
- setProperty* and getProperty* functionality to models

### Fixed
- Issue with converting Collection to json in response
- Error logging issues
- Request->file issues

### Removed
- Irrelevant packages from composer


## [0.1.3] - 2018-06-21
### Added
- make:env command for generating env file
- SimpleCURL for making cURL requests easily

### Changed
- components/logs folder moved to storage/logs
- components/data moved to storage/public
- components/lib renamed to libraries

### Fixed
- Slow development server fixed


## [0.1.2] - 2018-06-09
### Added
- Commands
- Local Development Server Support

## [0.1.1] - 2018-01-30
### Removed
- Unused styles and images

### Fixed
- Optimized homepage images

## [0.1.0] - 2018-01-29
- Initial release

[Unreleased]: https://github.com/avonnadozie/LiteFrame/compare/v0.3.0...HEAD
[0.1.1]: https://github.com/avonnadozie/LiteFrame/compare/v0.1.0...v0.1.1
[0.1.2]: https://github.com/avonnadozie/LiteFrame/compare/v0.1.1...v0.1.2
[0.1.3]: https://github.com/avonnadozie/LiteFrame/compare/v0.1.2...v0.1.3
[0.2.0]: https://github.com/avonnadozie/LiteFrame/compare/v0.1.3...v0.2.0
[0.3.0]: https://github.com/avonnadozie/LiteFrame/compare/v0.2.0...v0.3.0