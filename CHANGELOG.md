# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [2.1.2] - 2021-01-14
- Fix i18n "Warning: The string "..." has X different translator comments".
- Remove Travis hhvm test.
- Update wp-pay/core library to version 2.4.

## [2.1.1] - 2020-04-03
- Updated integration dependencies.
- Set plugin integration name.

## [2.1.0] - 2020-03-19
- Extend `Extension` class from `AbstractPluginIntegration`.

## [2.0.4] - 2019-12-22
- Improved error handling with exceptions.
- Updated payment status class name.

## [2.0.3] - 2019-08-26
- Updated packages.

## [2.0.2] - 2019-03-29
- Added support for additional payment data like customer, addresses, payment lines, etc.
- Added gateway for AfterPay, Bancontact, Bank Transfer, Credit Card, Focum, Giropay, Maestro, PayPal and SOFORT.

## [2.0.1] - 2018-12-12
- Update item methods in payment data.

## [2.0.0] - 2018-05-14
- Switched to PHP namespaces.

## [1.0.5] - 2017-09-14
- Implemented `get_first_name()` and `get_last_name()`.

## [1.0.4] - 2017-01-25
- Added filter for payment source description and URL.

## [1.0.3] - 2016-04-12
- No longer use camelCase for payment data.

## [1.0.2] - 2016-02-12
- Use WordPress pay core library version ^1.3.3.

## [1.0.1] - 2016-02-12
- WordPress Coding Standards optimizations.
- Removed status code from redirect in status_update.
- Added Pronamic gateway, with payment method selector in plugin settings.
- iDEAL gateway now uses the iDEAL payment method.

## 1.0.0 - 2015-05-26

### Added
- First release.

[unreleased]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.1.2...HEAD
[2.1.2]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.0.2...2.0.4
[2.0.3]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/1.0.5...2.0.0
[1.0.5]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-extensions/wp-e-commerce/compare/1.0.0...1.0.1
