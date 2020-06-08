# Changelog

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