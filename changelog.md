# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] - 2015-07-03
### Added
- PSR-7 support
- Dependency Injection Container

### Changes
- This version is based on middleware and most of the functionality has been moved to separate middleware.
- Classes and functionality has been moved to it's own repositories, see https://github.com/phapi.

### Removed
The following features has been removed for the 2.0.0 release. These will be moved to their own packages really soon but will **not** be a part of the default configuration.
- Redis cache
- XML serializer
- Yaml serializer
- CORS middleware
- Rate limit middleware

The following packages has been permanently been removed.
- PHP Serializer