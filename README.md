# zend-config-aggregator-parameters

[![Build Status](https://secure.travis-ci.org/zendframework/zend-config-aggregator-parameters.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-config-aggregator-parameters)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-config-aggregator-parameters/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-config-aggregator-parameters?branch=master)

Provides an extension to the `zendframework/zend-config-aggregator` to allow parameters within your configuration.
 
## Usage

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
  string(3) "qoo"
  'parameters' =>
  array(4) {
    'foo' =>
    string(3) "bar"
    'bar' =>
    array(2) {
      'baz' =>
      string(3) "qoo"
      'quux' =>
      string(3) "qoo"
    }
    'bar.baz' =>
    string(3) "qoo"
    'bar.quux' =>
    string(3) "bar"
  }
}

```

For more details, please refer to the [documentation](https://docs.zendframework.com/zend-config-aggregator-parameters/).

-----

- File issues at https://github.com/zendframework/zend-config-aggregator-parameters/issues
- Documentation is at https://docs.zendframework.com/zend-config-aggregator-parameters/
