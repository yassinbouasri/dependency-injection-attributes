# Container and Iterator with ServiceCollectionInterface

In the last chapter, we added these buttons *programmatically* from our remote. *But* when we did that, we broke the actual button-press functionality because we're no longer injecting a container.

Over in `ButtonRemote.php`, there's a couple of ways we could solve this. The first approach, which is probably the *easiest*, is to inject two arguments here: One that's an *iterator* and one for the container. That *would* work and it's perfectly valid, but there's a *better* way.

We *could* inject something that's both an iterator *and* a container - a `ServiceCollectionInterface`. Let’s take a look at that. This extends the `ServiceProviderInterface`. That's an `IteratorAggregate`, so in *addition* to being iterable, it's *also* `Countable`. That means we could *count* the services in there if that was a requirement.

If we look at `ServiceProviderInterface.php`, we can see that it extends `ContainerInterface`. So this is both a container *and* an iterator at the same time, and *that* saves us from having to inject two different things. *So*, let's switch this back to `AutowireLocator`. Now *this* will be injected and it'll be both an iterator and a service container. Convenient! I'll clean up some unused imports here, and... nice. Now this code should work as expected because we’ll be able to both loop over all of the buttons within the iterator and *still* be able to get and press each button through the container.

Let’s refresh our app. Okay, we’re *still* listing the buttons, so that’s a good sign. That part of our app is working again. If we jump back into our profiler and look at the `POST` request, you can see that we're still calling our logic in the `ChannelDownButton`, based on the button found via our Command pattern.

Okay, we need to talk about *laziness*. One of the great things about injecting service container is that the underlying button services aren't instantiated until we call `get()`. So if we call `get()` on `power`, only the "Power" button will be instantiated in our app - the others won't be.

This is a really nice way to improve performance because you don’t have to instantiate *every single button*. But this isn't without problems. As soon as we start iterating over the buttons, we actually *are* instantiating them. *So* we've kind of removed the lazy aspect, and we're still instantiating each button. For a *small* app like this one, it's not a big deal, but if we had *hundreds* of services in here, this wouldn't be a solid solution.

`ServiceCollectionInterface` to the rescue! We’re going to remove all of this code and `dd(buttons.getProvidedServices)`. Okay, let’s jump back to our browser and see what that looks like.

All right, we can see that this looks *very* similar to what we had back in Chapter 2. This method, `getProvidedServices()`, returns an array keyed by our button names. This is just some internal code that tells Symfony how to wire each of these services inside the service container. We need to get the array keys for this and return them. To do that, say `return array_keys()`. There we go! By calling `getProvidedServices()`, we're no longer instantiating *all* of the services just to get the keys. That should *significantly* improve performance, even in larger apps. If we head back to our browser and refresh... everything is working as expected again!

To be *doubly* sure that everything is working as expected, let's add a new button to our remote. All we need to do for our remote to register it is add a new button implementation. *So* add a new PHP class called `MuteButton`... and implement `ButtonInterface`. Then hold "control" + "enter", select "Implement methods", and implement `press`. Inside this, we'll just `dump('Mute button pressed.)`.

Now, if you recall from our other buttons, we need to add this `#[AsTaggedItem]` attribute. So let's do that... and we'll set the index to `mute`. We can leave the priority as the default, which is *zero*, because all of our other buttons are a higher priority; This one should just fall below the existing buttons.

Before we try this, there's one more thing we have to do. Each button has a SVG icon that's included in `assets/icons`. In the `tutorial/` directory, all we need to do is copy this `mute.svg` file that was already created for us and paste it into`assets/icons`. That's it!

*So*, to recap, when we're adding a new button, we need to create a service that implements `ButtonInterface`, add `#[AsTaggedItem]` with the name of the button, and then add the SVG to the `assets/icons/` directory. The template should render it correctly.

If we go to our app and refresh... there it is! Our button was *automatically* added to our remote. If we click it and check the profiler... it prints "Mute button pressed.", which is coming from the `MuteButton` service we just created. This is working as expected. *Awesome*!

So *that's it*! We've fully refactored our app and improved its performance using the Command pattern. Next: Let's add *logging* to our remote, and *do it* in a way that separates the concerns.
