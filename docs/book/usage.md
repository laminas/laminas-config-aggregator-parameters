# Basic Usage

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
