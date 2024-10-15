# Enforce Named Autowiring with Target

In the last chapter, we added logging to our button presses. Try this
out and check out the "Logs" profile panel. Notice this little "app" tag.
Symfony's logger, Monolog has the concept of log channels, which are like
*categories* for your log messages. The default channel is "app", but you can
create others to categorize your log messages.

We'll add a new channel called "button" for our button presses. Back in our
code, open `config/packages/monolog.yaml`. Under `channels`, add a new channel
called `button`. Perfect! Close this file.

We want `LoggerRemote` to use this new channel... but... how do we do that?

Open up `LoggerRemote`. We're injecting the `LoggerInterface` service, which
uses autowiring to inject the logger.

We're going to use something called "named autowiring". *Standard* autowiring
takes a look at the typehint and injects a service based on that. *Named
autowiring* looks at both the typehint *and* the argument name to determine
which service to inject.

We can use the `debug:autowiring` console command to see what services are
available for autowiring. Spin over to your terminal and run:

```terminal
bin/console debug:autowiring logger
```

The `logger` argument just filters the results down to services with "logger"
in the name. The first entry is `LoggerInterface` - note it does not have
any argument associated with it. This is the service you get if using *standard
autowiring*. The ones below all have arguments and here is our `$buttonLogger`.

To use the button logger, we need the argument for `LoggerInterface` to
be `$buttonLogger`. In `LoggerRemote`, rename this argument to `$buttonLogger`.

Now, head back to our app... refresh the page, and... press the "Channel Up"
button. Pop into the last request's profile and check out the "Logs" panel.
We're now logging our button presses to the `button` channel! *Nice*!

This is working like it's supposed to, but I'd like to talk
about a problem that can occur when using named autowiring this way. Imagine
a year from now, we decide to rename this variable to something simpler like
just `$logger`.

Jump back to our app, refresh the page, and press a button. It *seems* like it
still works but check out the "Logs" profile panel. The *message* is still
being logged, but if you look closely, it sneakily switched back the "app"
channel. Why did this happen?

When we renamed the argument, we broke named autowiring and switched back to
standard autowiring. Perhaps this isn't super problematic for logging, but
imagine something like a database or cache service being autowired
this way. This could be trouble...

We need a way to *enforce* named autowiring. Say hello to the `#[Target]`
attribute!

Over in our `LoggerRemote`, add `#[Target]` above the `LoggerInterface $logger`
argument. Inside, set the value to `buttonLogger` - this is the argument name
(without the `$`) we see in the `debug:autowiring` output. Now, the actual
PHP class argument name can be anything we want!

Jump back to the app, refresh, press "Volume Up", and check the "Logs" profile
panel. We're back to the `button` channel! *Sweet*!

To see what I mean by *enforcing* named autowiring, back in
`config/packages/monolog.yaml`, rename the channel to `buttons` (with an "s").
Back in the app, refresh, and... we see an error!

> Cannot autowire service "LoggerRemote": argument "$logger" of method
> "__construct()" has "#[Target('buttonLogger')]" but no such target exists.

Yes! Normally we don't celebrate errors, but this is a good one! We now have a
hard error when the named autowiring service cannot be found. This is what we
want!

We'll fix this by going back to `LoggerRemote` and updating the `#[Target]`
value to `buttonsLogger`.

Refresh the app and we're back in business! Press "Mute" and pop into the
"Logs" profile panel. Yep, we're logging to our renamed `buttons` channel!

I love named autowiring, but also love that we can enforce it with the
`#[Target]` attribute.

Next: We're going to add a *new* button, but *this time*, we'll make it
*conditional*, so it only appears in certain environments.
