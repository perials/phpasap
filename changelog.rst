#########
Changelog
#########

Version 1.2
===========

Release date: Not released

- Added environment based config overriding
- Added json response to View handler
- Added database profiler
- Added custom exception class
- Fixed a bug where function name having underscore didn't worked in routing callback
- Fixed Model class bug where a new connection to database was established for every DB::table() call
- Fixed Validation library bug where rest of validation rules were not skipped if first rule failed
- Modified validation rules