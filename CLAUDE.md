# constrained-morph-to-for-laravel

## Commands

```bash
composer test          # Run Pest test suite
composer analyse       # PHPStan (level 8)
composer format        # Pint code style
composer lint          # Pint + PHPStan combined
composer test-coverage # Tests with coverage report
```

## Architecture

- `src/ConstrainedMorphTo.php` — extends `MorphTo`; overrides `getResults()` and `buildDictionary()` to filter by allowed types
- `src/HasConstrainedMorphTo.php` — trait that exposes `constrainedMorphTo()` on Eloquent models
- `workbench/` — local Laravel app used by Orchestra Testbench for integration tests
- Tests are integration tests against a real (SQLite) database — do not mock it

## Release process

1. Push commits to `main`
2. Create a GitHub release with tag `vX.Y.Z` and release name `vX.Y.Z`
3. The `update-changelog` workflow commits the updated `CHANGELOG.md` automatically

```bash
gh release create vX.Y.Z --title "vX.Y.Z" --notes "<release body>"
```

Source the release body from the `[Unreleased]` section of `CHANGELOG.md`.

The workflow passes the **release name** as the version heading and the **release body** verbatim into `CHANGELOG.md`. Write release notes in Keep a Changelog format so they land correctly.

### Keep a Changelog format

Use `###` headings for change type sections. Only include sections that apply:

```markdown
### Added
- New feature or capability

### Changed
- Modification to existing behaviour

### Deprecated
- Feature to be removed in a future release

### Removed
- Feature that was removed

### Fixed
- Bug that was corrected

### Security
- Vulnerability patch
```

Rules:
- Entries are bullet points written for humans, not commit messages
- The `[Unreleased]` section's compare URL is updated automatically by the workflow
