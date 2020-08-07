# Changelog

## 1.2.0
### Added

- `JSend` JSend is a specification that lays down some rules for how JSON responses from web servers should be formatted.
- `Json::decode()`, `Json::decodeToObject()` and `Json::encode()`

## 1.1.0

### Added

- `Interfaces\TimePeriods`
- `Traits\GetPatternsHtml5AndPCRE`
- `Traits\Properties__get`
- Method `ArrayUtils::oneToMultiDimArray`

### Deprecated

- `Traits\ConvertPatternHtml5ToPCRE`

### Fixed

- Fixed error in `ArrayUtils::iteratorToArray` (nested objects `\Traversable` did not convert to an array)