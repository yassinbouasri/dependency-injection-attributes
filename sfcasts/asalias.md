# AsAlias and AsDecorator

Time to add a new feature! I want to add logging to the button presses so
we can keep track of what our minions are doing!

In `ButtonRemote`, we *could* inject the logger service and log the button
presses right here. But technically... this violates something called the
"single responsibility principle". That's just a fancy way of saying that
an object should only do one thing. In this case, this object handles
pressing buttons. Adding logging here would make it do two things.

Instead, we're going to use a design pattern called "Decoration". Fancy!
We're going to create a new class that wraps, or "decorates", our `ButtonRemote`.

In `src/Remote`, create a new PHP class called `LoggingRemote`. This is our
"decorator". We need it to have the same methods as the object it's decorating. So
copy the two methods from `ButtonRemote`, paste them here and remove their guts.
Add a constructor, and inject the logger service with
`private LoggerInterface` (the one from `Psr\Log`) `$logger`. Now, inject the
object we're decorating: `private ButtonRemote $inner`. I like to use `$inner`
as the parameter name when creating decorators.

In our methods, first, defer them to the inner object. In `press()`, write
`$this->inner->press($name);` and in `buttons()`, `return $this->inner->buttons()`.

Now, let's add logging to `press()`. Before the inner press, write
`$this->logger->info('Pressing button {$name}')` and add a context array
with `'name' => $name`. This curly brace stuff is a mini-templating system
used by Monolog, Symfony's logger. Copy this and paste below the inner press
and rename "Pressing" to "Pressed".

Decorator done!

To actually use our decorator, in `RemoteController`, instead of injecting `ButtonRemote`,
inject `LoggingRemote`.

Let's try it! Back in our app, press the power button and jump into the profiler
for the last request. We can see that the logic from `ButtonRemote` is still being
called. And if we check out the "Logs" panel, we see the messages!

You may have noticed our two remotes have the same methods - this is a sign that
we need a common interface. Create a new class in `src/Remote` called `RemoteInterface`.
Copy the `press()` method *stub* from `LoggerRemote` and paste it into `RemoteInterface`.
Do the same for `buttons()`.

Now let's have both our remotes implement this interface. In `ButtonRemote`, add
`implements RemoteInterface`. Do the same in `LoggingRemote`.

Take a look at `LoggerRemote`'s constructor. We're hardcoding `ButtonRemote` here
so let's change to `RemoteInterface`.

Back in the app, refresh and... Error!

> Cannot autowire service "LoggingRemote": argument "$inner" of method "__construct()"
> references interface "RemoteInterface" but no such service exists.

This error occurs when Symfony tries to autowire an interface but there are multiple
implementations. We need to tell Symfony which implementation to use. The error
message contains a hint:

> You should maybe alias this interface to one of these existing services:
> "ButtonRemote", "LoggingRemote".

Ah, we need to "alias" our interface. This tells Symfony which implementation to use.

We can do this with, you guessed it, another attribute: `#[AsAlias]`. This gets
added to the class that we want to use as the implementation. 

Hmm, which of our remote objects should be the alias? When using decoration, you'll want
the inner-most object to be the alias. In our case, that's `ButtonRemote` so
open that and add `#[AsAlias]`. This tells Symfony:

> Hey! When you need to autowire a `RemoteInterface`, use `ButtonRemote`.

Back in our app, refresh and... error's gone! Press the "volume up" button and check
the profiler. Button logic is still being called and if we check the "Logs" panel,
there's our messages.

Check out our `RemoteController`. We're still injecting a concrete instance of our
service. Change this to `RemoteInterface`.

Back in the app, press "channel down" and check the profiler. Button logic
is working, but our logs are gone!

Since we aliased `RemoteInterface` to `ButtonRemote`, Symfony doesn't know about
our decoration!

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
