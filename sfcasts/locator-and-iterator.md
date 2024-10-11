# Container and Iterator with ServiceCollectionInterface

In the last chapter, we added these buttons *programmatically* from our remote.
*But* when we did that, we broke the actual button-press functionality because
we're no longer injecting a container.

Over in `ButtonRemote.php`, there's a couple of ways we could solve this. The
first approach, which is probably the *easiest*, is to inject two arguments
here: One that's an *iterator* and one for the *locator*. That *would* work and
it's perfectly valid, but there's a *better* way.

We can inject an object that's both an iterator *and* a locator:
`ServiceCollectionInterface`. This is a `ServiceProviderInterface` (that's
the *locator*) *and* an `IteratorAggregate` (that's the *iterator*). For good
measure, it's also `Countable`.

We need to switch this back to `AutowireLocator` for Symfony to inject the
`ServiceCollectionInterface`.

I'll clean up some unused imports here, and... nice.

Back in our app, refresh and... Okay, we’re *still* listing the buttons, so
that’s a good sign. Now, if we click a button... it looks like this is working
again. Pop into the profiler to check the `POST` request and the proper button
logic is still being called. *Sweet*!

One of the great things about a service locator is it's *lazy*. Services aren't
instantiated until we call `get()`, and even then, only that single one.

I *love* being lazy but we have a problem. Down here, in `buttons()`, we're
iterating over all the buttons. This is *forcing* the instantiation of *all*
the button services just to get their `$name`'s. Since we just care about the
names, this is a waste of resources.

`ServiceCollectionInterface` to the rescue! Symfony service locators have
a special method called `getProvidedServices()`. Remove all this code and
`dd($this->buttons->getProvidedServices())` to see what it returns.

Jump back to our app and refresh. This looks almost identical to the manual
mapping we previously used with `#[AutowireLocator]`.

We want the *keys* of this array. Back here, return `array_keys()` of
`$this->buttons->getProvidedServices()`.

Go back to the app and... refresh. Everything is still working as expected and
behind the scenes, we're no longer instantiating *all* the button services.

Performance win!

To celebrate all the improvements we've made, let's add a new button to our
remote!

Create a new PHP class called `MuteButton`, implement `ButtonInterface`, and
add `#[AddTaggedItem]` with an `$index` of `mute`. Leave the priority as the
default, `0`. This will slot this button below the others.

There's just one other thing we need to do. Each button has an SVG icon in
`assets/icons` with the same name as the button. Copy `mute.svg` file from
`tutorials/` and paste it here.

Moment of truth! Go back to our app, refresh, and... there it is! Click it
and check the profiler. It's working as expected!

That's it for this refactor! Adding new buttons is now super simple *but*
still performant.

Next, let's add logging to our remote!
