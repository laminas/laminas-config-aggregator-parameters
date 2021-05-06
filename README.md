# laminas-config-aggregator-parameters

[![Build Status](https://travis-ci.com/laminas/laminas-config-aggregator-parameters.svg?branch=master)](https://travis-ci.com/laminas/laminas-config-aggregator-parameters)
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-config-aggregator-parameters/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-config-aggregator-parameters?branch=master)

Provides an extension to the `laminas/laminas-config-aggregator` to allow parameters within your configuration.

## Installation

Run the following to install this library:

```bash
$ composer require laminas/laminas-config-aggregator-parameters
```

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

## Documentation

Browse the documentation online at [https://docs.laminas.dev/laminas-config-aggregator-parameters](https://docs.laminas.dev/laminas-config-aggregator-parameters).

## Support

* Issues: [https://github.com/laminas/laminas-config-aggregator-parameters/issues](https://github.com/laminas/laminas-config-aggregator-parameters/issues)
* Chat: [https://laminas.dev/chat](https://laminas.dev/chat)
* Forum: [https://discourse.laminas.dev](https://discourse.laminas.dev)
