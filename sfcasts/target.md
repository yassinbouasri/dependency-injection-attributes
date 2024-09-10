# Enforce Named Autowiring with Target

Alright, and welcome back. So in the last chapter, we added logging. So when a button
is pressed, we log a before and after message to to the Symfony logger, which uses
monolog, which is which uses a third-party, official third-party package for Symfony
called monolog. So if we press a button, it's power pressed, and then jump into our
profiler for the last request, and take a peek at the logs panel here, and we can see
like we showed in the last chapter, there is a log message being shown before and
after a button is pressed, telling us what button was pressed. Now one thing to note
here is you see under here there this app, and both messages are under this app, have
this app tag. This represents the channel. monolog allows having different channels
to sort of categorize your log messages. The default channel is app, so if you do
nothing to customize the channel in any way, app will be used. So for this chapter, I
want to separate these messages into a new channel that we're going to call button.
So let's jump back to our, so let's jump into our code, and to add a channel to
monolog, we're actually going to use the, we're actually, this will be the only time
we're actually going to be in YAML. In, we're going to edit YAML for the first, and I
think only time in this whole chapter. So go to config, and it will be only, we're
going to, we're going to edit YAML now for the first and only time in this chapter,
in this course, and it's only going to be for bundle config. We're going to be
configuring the monolog bundle to add a new channel. So if we go to config, packages,
monolog.yaml, you can see under this key here, monolog channels, is where you can add
custom channels. So we will add our button channel. All right, so we can close this
file, and now if we go to our logger remote, we can see here that we're injecting the
logger interface, and this is using auto wiring to inject, to inject the logger. Auto
wiring, auto wiring, just plain old auto wiring allows you to type hint a service or
class name, an interface or class name that represents another service, and it will
automatically inject it. In this case, it's going to inject, what monolog does is for
each channel, it adds a separate, a separate service for each channel, a separate
logger interface for each channel. If using just the logger interface, you're going
to get that app, that app channel by default, but there's another, there's a couple
ways that we can inject the, our new button channel logger here. The one we're going
to look at is something called named auto wiring. So if standard auto wiring takes a
look at the type hint, and injects a service based on that, named auto wiring looks
at both the type hint and the argument name to determine what service to inject, and
we can see this quite clearly if we spin over to our terminal, and we will run bin,
console, debug, auto wiring, logger. The debug auto wiring command just shows you
anything in your app that you can currently auto wire, and we're just adding logger
on the end here to filter down to just services related to the logger. So we see a
fairly big list here. We can see the very first entry, this logger interface. This is
what, so this is just standard auto wiring because there's no secondary part of it.
So this is what we're actually doing, and we're getting just the standard monolog
logger, which will be associated with that app channel, which is the default. But if
you look at all these others down below, we can see that they both have the interface
name and the name of the argument that can be used. So we can see Symfony provides
most of these, a bunch of different channels for different Symfony specific events,
but here we can see that we have our button logger argument. So if we, so what this
is saying is if we change the argument name that we're injecting to button logger, we
will get our button channel logger service. So let's go back to our app and do just
that. So let's change this. We will go to refactor and rename and change this to
button logger. Okay, now let's head back to our app, refresh the page, and let's
press the channel up button. Okay, that worked, channel up pressed. But now if we go
down to our profiler and look at the logs panel, we can see now that the log message
is still being shown here, but you can see now it's using the button channel. So this
is great. This is working just fine. But I want to demonstrate a little bit of a
problem that can occur when using named auto wiring this way.
Let's say, you know, a year from now we are looking at this app and we're like, you
know, this, I don't like this variable name. I want to rename it to just Logger. You
know, we've, we've kind of forgotten or another user is using it that's not, that's
not realizing that we're using named auto wiring here. So let's rename it to back to
just Logger. And now let's jump back to our app. We'll refresh the page, press a
button, seemingly still works. If we go to our Logger channel here, if we go to our
Logger panel in the profiler, we can see, you know, the, the message is still being
logged. But if you look carefully, it's sneakily switched back to app. So why is
this? The reason is we've basically disabled named auto wiring for our Logger when we
inject it. Now, for logging, it likely isn't a terrible, like a terribly problematic.
It just is not going to be categorized into the, into the channel that you expect. It
certainly could be a problem if you're, you know, going through logs from, you know,
a whole bunch of logs and trying to filter down on the button channel, you all of a
sudden will stop seeing, seeing entries in there. But you can imagine for more
complex or system critical things like caching, this could be a problem is you could
inadvertently switch the, the cache pool that you're, that you're expecting to, to
cache things to and that could be problematic. So the way to solve this in a way that
will actually give us an error if we've changed our, if we've changed our named
argument, our named auto wiring setup, is another dependency injection attribute
called target. So if we add it here, so target, and then for the name, what we'll
want to do is this will be the exact name that we see in, in our, in our output here
and what we had before as the variable name, but it'll just be a string. So if we
jump back here and we'll call this button logger. Perfect. Okay, so that's set up and
we can keep, and now we can keep the variable name, whatever we want. It doesn't
matter that it's not button logger anymore because this target tells it what tells us
what the target, what the named auto wiring service is. So now if we jump back to our
app, we'll refresh the page, we'll press volume up. Okay, volume up pressed, and then
go to our logging panel in the profiler and sure enough, it's still working, volume
up, and now it's back to using our button channel. So this may not look like much. We
just fixed the problem, but the key here is let's rename the, the, the, the, the,
the, the, the, rename the, the channel. So let's go to config routes. Sorry. Let's go
to config packages, monolog, and set a button. Let's just call it buttons. So now
we've just renamed the channel. Now, if we go back to our app and refresh, we get now
an error, and this is what we want. So it's telling us we cannot auto wire. We cannot
auto wire this on the logger service of method construct because this target is set
to button logger and button logger no longer exists. It's now buttons logger with an
S. So this is what we want because now the container cannot be built. So we've
enforced the usage of this named auto wiring service. So to fix this, we just have to
go back to our app and find our logger remote and in the target, let's just add that
S, save, jump back to our app, refresh. We'll press a button. We'll press mute. Then
go back to the profiler into the logs panel. And sure enough, it's now being logged
to our renamed buttons channel, and that's now being fully enforced by Symfony. All
right. So this is a really nice way to use named auto wiring, but also enforce that
the proper service is being used because without target, the service is not enforced.
As you saw, it just defaults back to the default auto wiring for that service. All
right. So next we are going to look at adding a new button, but have it conditional
on what environment, have it only show under certain environments. That's next.
