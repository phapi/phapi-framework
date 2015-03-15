# Phapi
Phapi is a PHP based framework aiming at rapid and simplified API development as well as focusing at performance and keeping the code base small and simple.

## Version 2
The second version of Phapi is under development. This develop branch contains version 2 while the master branch still contains version 1.

This version will take advantage of the relevant PSR standards:

- PSR-1: Coding Standard
- PSR-2: Coding Style Guide
- PSR-3: Logger Interface
- PSR-4: Autoloading
- PSR-7: Http Message

Phapi will also use a Dependency Injector Container so that middleware can use and store dependencies as well as any other information a middleware needs to be able to share.

**Please note that version 2 will be in beta until PSR-7 are finalized and approved. Updates might break backward compatibility.**

It is not regularly tested against HHVM, but there is nothing in this package that should fail against HHVM. However, support is not guaranteed.

### Documentation
The documentation will be moving from the readme.md file to it's own repo and web page. A link will be added here as soon as the repo and webpage is up.

## License
Phapi is licensed under the MIT License - see the LICENSE file for details

## Contribute
Contribution, bug fixes etc are [always welcome](https://github.com/ahinko/phapi/issues/new).
