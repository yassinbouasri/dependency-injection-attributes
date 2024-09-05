# Container and Iterator with ServiceCollectionInterface

All right, so in the last chapter, we added these buttons programmatically from our remote, but we broke the functionality
of actually pressing the buttons because we are no longer injecting a container.

If we open our button remote, there's a couple of ways we could solve this. Number one, which is maybe the easiest, is we
could inject two arguments here: one that is an iterator and one for the container. That would work and be perfectly
valid, but there is a better way.

There is a way that we can inject something that is both an iterator and a container. What you want to inject here is
called a `ServiceCollectionInterface`. Let’s take a look at this interface. This extends the `ServiceProviderInterface`.

You can see here, it's an `IteratorAggregate`, so in addition to being iterable, it's also `Countable`. This means we
can count the services in there if that was a requirement. If we look at `ServiceProviderInterface`, you can see this
just extends `ContainerInterface`.

So, this is essentially both a container and an iterator at the same time, which saves us from having to inject two
different things. Now, instead of injecting, we're going to switch this one back to `AutowireLocator`.

Now this will be injected and will be both an iterator and a service container. Let’s clean up some unused imports here.
Now this code should work as expected because we’ll be able to both loop over all the buttons within the iterator and
still be able to get and press each button through the container.

Let’s refresh our app. OK, we’re still listing the buttons, so that’s a good sign. This is working again. If we jump
back into our profiler and look at the post request, we can see we're still calling our logic in the `ChannelDownButton`,
based on the button found via our command pattern.

OK, so one thing we need to talk about a little bit is laziness. One great thing about the service container being
injected is that the underlying button services are not instantiated until you call `get` on it. So if we call `get` on
`power`, only the power button will be instantiated in our app, and the other ones won't be.

This is a really nice way to improve performance because you don’t have to instantiate every button. But we have a
little bit of a problem here. As soon as we start iterating over the buttons, we're actually instantiating them.

So, we've kind of removed the laziness, and now we're instantiating each button. For our small app, it's not a big deal,
but you can imagine if we had hundreds of services in here, this would not be a good solution.

So, the `ServiceCollectionInterface` can come to the rescue here. We’re going to remove all this code and `dd` this:
`buttons.getProvidedServices`. Let’s jump back to our browser and see what that looks like.

OK, we can see this looks very similar to what we had back in chapter two. We can see that this method,
`getProvidedServices`, returns an array keyed by our button names. This is just some internal code that tells Symfony
how to wire up each of these services inside the service container.

What we need is to just get the array keys of this and return them. So, let's just do `return array_keys`. There we go.
By calling `getProvidedServices`, we're no longer instantiating all the services just to get the keys, which greatly
improves the performance, especially in larger apps.

So now, let's go back to our browser and refresh. Everything is back working as expected.

All right, so to be doubly sure that all this is working as expected, let's add a
new button to our remote. And remember, all we should need to do is... All we should
need to do for our remote to pick it up is now add a new button implementation.

So we'll add a new PHP class and we'll call this one a mute button. And we'll
implement `ButtonInterface`. Then we'll do option-enter and control-enter to
implement methods. We'll implement `press`. And for this, we're just going to dump
"mute button pressed".

Now, if you'll remember from our other buttons, we need to add this `AsTaggedItem`
attribute. Again, we're just in our `MuteButton` class. We don't have to go anywhere
else. So we'll add this `AsTaggedItem` and we will set the index to mute. We are
going to leave the priority as the default, which is priority zero, because all of
our other buttons are a higher priority.

So this one should just slot in below the existing ones. And now if we go to... If
you remember, there's one other thing we have to do. Each button has an SVG icon
that's included in `assets/icons`. So if you go to our tutorial directory and just
copy the mute button up into icons, it's already created for us.

So that's the last thing we need to do. So remember, when we're adding a new button,
we need to create a service that implements `ButtonInterface`, add an `AsTaggedItem`
with the name of the button, and then add our SVG to our `assets/icons/` directory. The template
will render it correctly.

Now, if we go to our app and refresh, there it is. We have our button automatically
added to our remote. And then if we click it, it prints "mute pressed". We'll check
our profiler: mute button pressed coming from the mute button service that we just
created.

So perfect. It's working as we expect.

OK, and that's it! Our app is now fully refactored, performant, and using the command pattern. We're able to list our
buttons programmatically, and it’s all done in the most performant way possible.

Next, we’re going to add logging to our remote, and we're going to do it in a way that separates the concerns. So, that's
next.

