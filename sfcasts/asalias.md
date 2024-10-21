# AsAlias and AsDecorator

Time to add a new feature! I want to add logging to the button presses so
we can keep track of what our minions are doing!

In `ButtonRemote`, we *could* inject the logger service and log the button
presses right here. But technically... this violates something called the
"single responsibility principle". That's just a fancy way of saying that
a class should only do one thing. Right now, this class handles
pressing buttons. Adding logging here would make it do two things. And that's
probably fine, but let's challenge ourselves!

Instead, we'll use a design pattern called "Decoration" by
creating a new class that wraps, or "decorates", `ButtonRemote`.

In `src/Remote/`, create a new PHP class called `LoggingRemote`. This is our
"decorator" and it needs the same methods as the class it's decorating. 
Copy the two methods from `ButtonRemote`, paste them here and remove their guts.
Add a constructor, and inject the logger service with
`private LoggerInterface` (the one from `Psr\Log`) `$logger`. Now, inject the
object we're decorating: `private ButtonRemote $inner`. I like to use `$inner`
as the parameter name when creating decorators.

In each method, first, defer to the inner object. In `press()`, write
`$this->inner->press($name);` and in `buttons()`, `return $this->inner->buttons()`.

*Now* let's add the logging. Before the inner press, add
`$this->logger->info('Pressing button {$name}')` and add a context array
with `'name' => $name`. This curly brace stuff is a mini-templating system
used by Monolog, Symfony's logger. Copy this, paste below the inner press
and change "Pressing" to "Pressed".

Decorator done!

To actually *use* this decorator, in `RemoteController`, instead of injecting `ButtonRemote`,
inject `LoggingRemote`.

Let's try it! Back in our app, press the power button and jump into the profiler
for the last request. We can see that the logic from `ButtonRemote` *is* still being
called. And if we check out the "Logs" panel, we see the messages!

The two remote classes have the same methods, which this is a sign that
we could use a common interface. Create a new class in `src/Remote/` called `RemoteInterface`.
Copy the `press()` method *stub* from `LoggerRemote` and paste it here.
Do the same for `buttons()`.

Next, make both remote classes implement this interface. In `ButtonRemote`, add
`implements RemoteInterface`... and do the same in `LoggingRemote`.

In `LoggerRemote`'s constructor, Change `ButtonRemote` to `RemoteInterface`. We
don't *have* to do this, but now that we have an interface, that's really the
best thing to type-hint.

Back in the app, refresh and... Error!

> Cannot autowire service `LoggingRemote`: argument `$inner` of method `__construct()`
> references interface `RemoteInterface` but no such service exists.

This happens when Symfony tries to autowire an interface but there are multiple
implementations. We need to tell Symfony *which* of our two service to use when we
type-hint `RemoteInterface`. And the error even gives us a hint!

> You should maybe alias this interface to one of these existing services:
> "ButtonRemote", "LoggingRemote".

Ah, we need to "alias" our interface. Technically, this will create a new service
whose id is `App\Remote\RemoteInterface`, but is really just an alias - or a pointer -
to one of our *real* remote services. 

Do this with, you guessed it, another attribute: `#[AsAlias]`. In `ButtonRemote`,
our inner-most class, add `#[AsAlias]`.

This tells Symfony:

> Hey! When you need to autowire a `RemoteInterface`, use `ButtonRemote`.

Back in our app, refresh and... the error is gone! Press "volume up" and check
the profiler. The button logic is still being called and if we check the "Logs" panel,
there are our messages.

Open up `RemoteController`: we're still injecting a concrete instance of our
service. That's fine, but we can be fancier now and use `RemoteInterface`.

Back in the app, press "channel down" and check the profiler. The button logic
is working, but our logs are gone!

Because we aliased `RemoteInterface` to `ButtonRemote`, Symfony doesn't know about
our decoration! When it sees the `RemoteInterface` type-hint, it injects `ButtonRemote`,
not `LoggingRemote`.

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
