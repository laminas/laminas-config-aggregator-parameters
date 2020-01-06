# laminas-config-aggregator-parameters

[![Build Status](https://travis-ci.com/laminas/laminas-config-aggregator-parameters.svg?branch=master)](https://travis-ci.com/laminas/laminas-config-aggregator-parameters)
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-config-aggregator-parameters/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-config-aggregator-parameters?branch=master)

Provides an extension to the `laminas/laminas-config-aggregator` to allow parameters within your configuration.
 
## Usage

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
                'quux' => '%foo%', 
            ],
        ]),
    ]
);

var_dump($aggregator->getMergedConfig());
```

Result:

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

For more details, please refer to the [documentation](https://docs.laminas.dev/laminas-config-aggregator-parameters/).

-----

- File issues at https://github.com/laminas/laminas-config-aggregator-parameters/issues
- Documentation is at https://docs.laminas.dev/laminas-config-aggregator-parameters/
