# Decorate a Service with AsDecorator

In the last chapter, when we used `#[AsAlias]` to alias our `RemoteInterface` to
`ButtonRemote`, this broke our logging. Symfony now just ignores `LoggerRemote`.

We need to tell Symfony that `ButtonRemote` is decorated by `LoggerRemote`. To do
this, we need to use another attribute: `#[AsDecorator]`.

`LoggerRemote` is the decorator so open that, and add `#[AsDecorator]`. The first
argument is the service it decorates: `ButtonRemote::class`.

This tells Symfony:

> Hey, when you need a `ButtonRemote`, give `LoggerRemote` instead.

Symfony essentially *swaps* the services and makes `ButtonRemote` the "inner" service
to `LoggerRemote`. This solidifies the need for the `RemoteInterface` we
created earlier. If we tried to inject `ButtonRemote` directly, we'd
get a type error because Symfony would be trying to inject `LoggerRemote`.
Because `RemoteInterface` is an alias for `ButtonRemote`, it can be swapped
for a different `RemoteInterface` implementation without issue.

Spin back to the app, refresh and... press "volume up". Check the "Logs" profiler
panel and... we're logging again!

Using `#[AsDecorator]` makes it super easy to add multiple decorators. Maybe we want
to add a rate limiting decorator to prevent the kids from mashing buttons. We'd just
need to create a `RateLimitingRemote` class that implements `RemoteInterface` and add
`#[AsDecorator(ButtonRemote::class)]`. Easy peasy!

Next: We'll add a custom logging channel and explore "named autowiring"!
