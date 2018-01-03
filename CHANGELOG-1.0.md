# LiteFrame 1.0 Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [Unreleased]
- Migrations *
- Optimise for API calls
- Whoops *
- Route grouping
- Handle uploaded files in Request object
- Installer/Updater interfaces for
  - First time run
  - Running migration
  - Other server setup/management tools
  - And allow changing of default routes (e.g /install, /update, /migrate or /manage)
- Validators for Controllers
- Enforces html output escaping for views or find a convenient way to use blade/smarty like view template
- Full Documentation
- Theme Inheritance *
- Good and robust ORM/Query Builder. should be light, support migration, relationships and also secure. 
    Checkout:
  - https://redbeanphp.com
  - Default DB to SQLite maybe
- Inbuilt support for mailing (other than php mail) *
- Allow Dependency injection (Request, Response, Custom Services etc) *
- Auto sitemap generation *

\* Not required for version 1.0. Bonus point if it's added. 


## [1.0.0] - 2017-06-20
### Added
- Job Scheduling

### Changed
- 

### Fixed
- 

### Removed
- 

### Deprecated
- 

### Security
- 


[Unreleased]: https://github.com/avonnadozie/LiteFrame/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/avonnadozie/LiteFrame/compare/v0.3.0...v1.0.0