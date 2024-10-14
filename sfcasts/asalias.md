# Alias an Interface with AsAlias

Ok, let's add a new feature. I'd like to add button press logging keep an eye
on what the kids are doing!

In `ButtonRemote`, we could inject the logger service and log before and after
each button press. Technically though... this does violate the *single responsibility
principle*. This class should only be responsible for pressing buttons, not
logging. We'll use *decoration* instead. Ooo fancy!

First, create a `RemoteInterface` in the `Remote` directory. This will have the
two public methods from our `ButtonRemote` so copy and paste just the signatures
of these.

Now, we'll implement this interface in `ButtonRemote`:

PhpStorm is happy, so we did this correct, perfect!

Next, in `ButtonController`'s `index()` method, instead of injecting
`ButtonRemote`, inject `RemoteInterface`. Symfony can autowire concrete classes
and interfaces, but when possible, use interfaces. This makes it easier to swap
out implementations later.

Back in our app, refresh, press a few buttons... Great, it's all still working!

We're ready to add a new implementation of `RemoteInterface` for the logger.
In `App\Remote`, create a new `LoggerRemote` class and have it implement
`RemoteInterface`. Press `Ctrl+Enter` to implement its methods.

Add a constructor and inject the logger service:
`private LoggerInterface $logger` (the one from `Psr\Log`). Since we want this
service to decorate another `RemoteInterface`, inject that as well:
`private RemoteInterface $inner`. The name `$inner` isn't required but is a
common convention I like to use with decorators.

First, implement the `buttons()` method. We'll just defer to the inner service
as we don't require any logging here. `return $this->inner-buttons();`.

In `press()`, we'll defer to the inner service: `$this->inner->press($name);`.

Now we can add logging! Before the press, write
`$this->logger->info('Pressing button {name}')`. For the second argument, the
context, write `['name' => $name]);`. This `{name}` syntax is a little template
system that Monolog, Symfony's logging package uses. After the `press()`, copy
the before line and paste it, changing `Pressing` to `Pressed`.

Decorator done!

Hop back to the app and refresh... Uh oh! An error!

> Controller "App\Controller\RemoteController::index" requires the "$remote"
> argument that could not be resolved...
> It references the "App\Remote\RemoteInterface" interface but no such service
> exists...

Hmm, what's going on here? 

We now have two services that implement `RemoteInterface`, and since we're
autowiring the interface, Symfony doesn't know which one to use. The next part
of the error gives us a hint on how to fix:

> You should maybe alias this interface with one of these existing services:
> "App\Remote\ButtonRemote", "App\Remote\LoggerRemote".

Ok, we need to *alias* the interface to one of the services that implement it.
Which one though? Well, we want it to use `ButtonRemote` because that's the
implementation that actually has the button press logic.

In `ButtonRemote`, add the `#[AsAlias]` attribute above the class. This tells
Symfony:

> Hey! Whenever you need to autowire the `RemoteInterface`, use the
> `ButtonRemote` service.

Now back to the app and refresh... it's working again!

But... our `LoggerRemote` isn't actually being used. Next, we'll fix that using
Symfony service decoration.
