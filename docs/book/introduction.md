# Introduction

This package supplies a [laminas-config-aggregator post processor](https://docs.laminas.dev/laminas-config-aggregator/config-post-processors/)
that consumes the [Symfony DependencyInjection ParameterBag](https://symfony.com/doc/current/configuration/using_parameters_in_dic.html)
in order to allow users to define parameters to re-use within their
configuration.

As an example, one could define an API key, cache path, or other common
filesystem location _once_ as a _parameter_, and then refer to that parameter
_multiple times_ within the configuration, in order to simplify updates to the
value.

Parameters are referenced within configuration using `%name%` notation.
Parameters may be defined as nested associative arrays as well; in such cases, a
`.` character references an additional layer of hierarchy to dereference:
`%foo.bar%` refers to the paramter found at `'foo' => [ 'bar' => 'value' ]`.

> Available since version 1.1.0

Parameters which reference other parameters can also be used.
