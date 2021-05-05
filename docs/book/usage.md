# Basic Usage

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

If you wish to use a literal `%name%` within your configuration, you **must**
double-escape the percentage signs: `%%name%%`. Failure to do so will result in
an exception when post-processing the configuration.

As a self-contained example:

```php
use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregatorParameters\ParameterPostProcessor;

$aggregator = new ConfigAggregator(
    [
        new ArrayProvider([
            'parameter_usage' => '%foo%',
            'parameter_name' => '%%foo%%',
            'recursive_parameter_usage' => '%bar.baz%',
            'parameterized_parameter_usage' => '%bar.quux%',
        ]),
    ],
    null,
    [
        new ParameterPostProcessor([
            'foo' => 'bar',
            'bar' => [
                'baz' => 'qoo',
                'quux' => '%foo%', // Since 1.1.0
            ],
        ]),
    ]
);

var_dump($aggregator->getMergedConfig());
```

The result of the above will be:

```php
array(5) {
  'parameter_usage' =>
  string(3) "bar"
  'parameter_name' =>
  string(5) "%foo%"
  'recursive_parameter_usage' =>
  string(3) "qoo"
  'parameterized_parameter_usage' =>
  string(3) "bar"
  'parameters' =>
  array(4) {
    'foo' =>
    string(3) "bar"
    'bar' =>
    array(2) {
      'baz' =>
      string(3) "qoo"
      'quux' =>
      string(3) "bar"
    }
    'bar.baz' =>
    string(3) "qoo"
    'bar.quux' =>
    string(3) "bar"
  }
}
```

## Parameter Lazy Loading

> Available since version 1.4.0

If your parameters are resolved from a database, redis, consul, or any other i/o
resource, you can use the `LazyParameterPostProcessor` which consumes just a
`callable` which can provide the parameters.

In case you are using config-caching, the i/o is not executed when performed in
the `callable`.
