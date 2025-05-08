# Changelog

## 1.0.0-5.0+

- Changes to the popover template:
  - `help_icon.mustache` now uses the new Bootstrap 5 attributes adopted in Moodle core.
  - Popovers are now correctly dismissed when clicking outside of them, allowing the removal of the AMD JS script tweak.

## 1.8.0

- Add new setting `initialsonly`:
  - Introduces an option to display only users' initials instead of their full first names.
  - Adds two new language strings: `$string['initialsonly']` and `$string['initialsonly_help']`.
- User Interface improvements:
  - Relocated the popover (help) icon to the right side of each row.
  - Moved the user messaging button into the popover.
