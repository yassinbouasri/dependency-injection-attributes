# Decorate a Service with AsDecorator

We need to configure "service decoration" with another attribute: `#[AsDecorator]`.

`LoggingRemote` is the decorator so open that, add `#[AsDecorator]`. The first
argument is the service it decorates: `ButtonRemote::class`.

Spin back to the app, refresh and... press "volume up". Check the "Logs" profiler
panel and... we're logging again!

Using `#[AsDecorator]` makes it super easy to add multiple decorators. Maybe we want
to add a rate limiting decorator to prevent the kids from mashing buttons. We'd just
need to create a `RateLimitingRemote` class that implements `RemoteInterface` and add
`#[AsDecorator(ButtonRemote::class)]`. Easy peasy!

Next: We'll add a custom logging channel and explore "named autowiring"!
