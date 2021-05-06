# Parameter Lazy Loading

> Available since version 1.4.0

If your parameters are resolved from a database, redis, consul, or any other i/o
resource, you can use the `LazyParameterPostProcessor` which consumes just a
`callable` which can provide the parameters.

In case you are using config-caching, the i/o is not executed when performed in the `callable`.
