# Regex Spider

Finds page content by regular expression recursively on all pages of specific site.

You specify:
1. start URL to begin crawling from it 
2. regular expression for content being searched

Spider crawls only initial domain and does not follow other domains.

Regex pattern should be same format which php function [preg_match](https://www.php.net/manual/en/function.preg-match.php) expects

## Setup

1. Setup external php dependencies

* via script (requires `docker`)
```bash
./composer_install.sh
```

* or  manually
```bash
composer install
```

## Usage

Script which prints data in csv format (requires `docker`):
```bash
./getcsv.sh URL REGEX
```
