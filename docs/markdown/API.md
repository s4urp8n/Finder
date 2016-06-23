# Zver\Finder

## Table of Contents

* [Finder](#finder)
    * [instance](#instance)
    * [addDirectories](#adddirectories)
    * [addDirectory](#adddirectory)
    * [addDirectoryFirst](#adddirectoryfirst)
    * [getDirectories](#getdirectories)
    * [resetDirectories](#resetdirectories)
    * [resetFilters](#resetfilters)
    * [filter](#filter)
    * [find](#find)
    * [limit](#limit)

## Finder

Class to find files and folders, apply callbacks to it and etc



* Full name: \Zver\Finder


### instance

Create instance of class and set directory to find if it is set

```php
Finder::instance( null|string $directoriesToSearch = null ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$directoriesToSearch` | **null&#124;string** | Directory path to search files or directories |




---

### addDirectories

Add multiple directories or directory to scan list

```php
Finder::addDirectories( string|array $directories ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$directories` | **string&#124;array** |  |




---

### addDirectory

Add directory to the end of scan list

```php
Finder::addDirectory(  $directory ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$directory` | **** |  |




---

### addDirectoryFirst

Add directory to beginning of scan list

```php
Finder::addDirectoryFirst(  $directory ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$directory` | **** |  |




---

### getDirectories

Get current directories list

```php
Finder::getDirectories(  ): array
```







---

### resetDirectories

Clear directories list

```php
Finder::resetDirectories(  )
```







---

### resetFilters

Reset filter callback

```php
Finder::resetFilters(  ): $this
```







---

### filter

Set filter callback

```php
Finder::filter( callable $callback ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callback` | **callable** |  |




---

### find

Run search and return array of results

```php
Finder::find(  $excludeRoot = false ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$excludeRoot` | **** |  |




---

### limit

Set limit to results array

```php
Finder::limit(  $number ): $this
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$number` | **** |  |




---



--------
> This document was automatically generated from source code comments on 2016-06-23 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
