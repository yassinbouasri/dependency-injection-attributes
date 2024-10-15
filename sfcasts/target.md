# Enforce Named Autowiring with Target

Welcome back! In the last chapter, we added *logging*, so when a button is pressed, we log a "Before" and "After" message to the Symfony logger, which uses an official third-party package for Symfony called Monolog. If we press the "Power" button for example, the message here reads "Power pressed", and if we jump into the profiler for the last request... and take a peek at the Logs panel here... we can see a log message from before and after our button press that tells us *which* button was pressed.

One thing to note here is "app". Both messages have this "app" tag. That represents the *channel*. Monolog allows you to utilize multiple channels to *categorize* your log messages. The *default* channel is "app", so if you don't customize a channel in any way, "app" will be used. In this chapter, we're going to *separate* these messages into a new channel called "button". Let's do this!

Back in our code, to add a channel to Monolog, we actually need to edit a YAML file for the first and maybe *only* time in this course. In `config/packages/monolog.yaml`, under `monolog` `channels`, *this* is where we can add a custom channel that we'll call `button`. Perfect! Close this file, and *now*, if we go to our `LoggerRemote`, we can see that we're injecting the `LoggerInterface`, which uses autowiring to inject `$logger`. Plain old autowiring allows us to typehint an interface or class name that represents another service, and inject it automatically. *Monolog* adds a *separate* `LoggerInterface` for each channel.

If you *just* use `LoggerInterface`, it will use the "app" channel by default. *We* want to use our new `button` channel, and there's a couple of ways we can do that. The way I'll show you uses something called "named autowiring".

*Standard* autowiring takes a look at the typehint and injects a service based on that. *Named autowiring* looks at both the typehint *and* the argument name to determine which service to inject, and you can see that if you spin over to your terminal and run:

```terminal
bin/console debug:autowiring logger
```

The `debug:autowiring` command shows you anything in your app that's currently autowireable, and adding `logger` on the end helps filter services related to the logger. This is a *pretty big* list, and the very first entry is `LoggerInterface`.

This is just standard autowiring because there's no secondary piece here. We're just getting just the standard `monolog.logger`, which is associated with the default "app" channel. *But* if you look at all the others below this, they have both the interface name *and* the name of the argument we can use. Symfony provides most of these

So we can see Symfony provides most of these channels for different Symfony-specific events. But *here*, we have our `$buttonLogger` argument. This basically says that if we change the argument name we're injecting to `$buttonLogger`, we'll get the `button` channel logger service. Let's do that!

Back in our code, hover over `$logger`, right click, and select "Refactor", then "Rename...", and change this to `$buttonLogger`. Now, head back to our app... refresh the page, and... press the "Channel Up" button. Hey hey! That worked - "Channel up pressed". But if we go to our profiler and look at the Logs panel... the log message is still being shown here, but *now* it's using the `button` channel. Nice! This is working like it's supposed to, and that's great, but I'd like to talk about a tiny problem that can occur when we use named autowiring this way.

Imagine that, a year from now, we're looking at this app and we just don't like this variable name anymore. We want to rename it to something simpler like `$logger`. If we jump back to our app... refresh the page... and press a button... it *seems* like it still works. But if we open the profiler and look at the Logs panel again, the *message* is still being logged, but if you look closely, it sneakily switched back to "app". Why did that happen?

The *reason* is that we've basically *disabled* named autowiring for our logger when we inject it. It isn't super problematic for logging, even though it won't be categorized into the channel we'd expect, but it *could* be a problem if we're going through a bunch of logs trying to filter down to the `button` channel and we stop seeing entries all of the sudden. It could also be an issue for more critical things like caching because we could inadvertently switch the cache pool we're trying to cache things to.

The way to solve this and receive an error message if we change our named autowiring setup is by implementing *another* dependency injection attribute called "target". Over in our code, inside the constructor, add `#[Target]`. The *name* will be the exact name we see in our output, but just a string, so let's call this `buttonLogger`. *Perfect*.

Now that we have that set up, we can make the variable name whatever we want. It doesn't matter that it isn't `buttonLogger` anymore because `#[Target]` tells us what the named autowiring service is. If we head back to our app... refresh the page... and press "Volume Up"... we see "Volume up pressed". So far, so good! And if we open the profiler check the Logs... it's still working and it's back to using our `button` channel. *Sweet*! While this may not look like much, we just fixed the problem. To *really* test this out, let's rename the channel.

Back in `config/packages/monolog.yaml`, set a button that we'll just call `buttons`. We've just renamed the channel, so if we go back to our app and refresh... we now see an error. That's what we're looking for! We can't autowire this on the `$logger` service of `method "__construct"` because `#[Target]` is set to `buttonLogger`, which no longer exists; It's now "*buttons* logger" with an "s". The container can't be built, so we're *enforcing* the use of this named autowiring service.

To fix this, we just need to go back to our code, open `LoggerRemote.php`, and in the `#[Target]`, add that pesky "s". Save that, jump back to our app... refresh... press the "Mute" button this time, and check the profiler again. In Logs, *sure enough*, it's now being logged to our `buttons` channel, which is *enforced* by Symfony.

All right! This is a really nice way to use named autowiring, while also ensuring the proper service is being used, because without `#[Target]`, the service is *not* enforced - it just uses the *default* autowiring for that service.

Next: We're going to add a *new* button, but *this time*, we'll make it *conditional*, so it only appears in certain environments.
