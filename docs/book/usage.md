# Usage

This package supplies a [zend-config-aggregator post processor](https://docs.zendframework.com/zend-config-aggregator/config-post-processors/)
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

If you wish to use a literal `%name%` within your configuration, you **must**
double-escape the percentage signs: `%%name%%`. Failure to do so will result in
an exception when post-processing the configuration.

As a self-contained example:

```php
use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregatorParameters\ParameterPostProcessor;

$aggregator = new ConfigAggregator(
    [
        new ArrayProvider([
            'parameter_usage' => '%foo%',
            'parameter_name' => '%%foo%%',
            'recursive_parameter_usage' => '%bar.baz%',
        ]),
    ],
    null,
    [
        new ParameterPostProcessor([
            'foo' => 'bar',
            'bar' => [
                'baz' => 'qoo',
            ],
        ]),
    ]
);

var_dump($aggregator->getMergedConfig());
```

The result of the above will be:

```php
array(3) {
  'parameter_usage' =>
  string(3) "bar"
  'parameter_name' =>
  string(5) "%foo%"
  'recursive_parameter_usage' =>
  string(3) "qoo"
  'parameters' =>
  array(3) {
    'foo' => 
     string(3) "bar"
    'bar' =>
    array(1) {
      'baz' =>
      string(3) "qoo"
    }
    'bar.baz' =>
    string(3) "qoo"
  }
}
```
