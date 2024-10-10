# Simplify with AutoconfigureTag and AsTaggedItem

Welcome back! In the last chapter, we refactored the big `switch` statement in
our controller to this little try-catch. Our remote works the same way it did
before, but now we're using the *Command pattern*. We also used
this `AutowireLocator` attribute to tell Symfony how to wire up this container.
This is *great*, but what if I told you we could simplify this *even* more?

Since all of our buttons implement this `ButtonInterface`, we'll add our
*second* dependency injection attribute: `#[AutoconfigureTag]`. This will add a
tag to all of the services that implement `ButtonInterface`. This attribute
tells Symfony:

`Hey, add a tag to any service that implements this interface!`

A *tag* is just a string that's connected to a service, and by *itself*, it
doesn't really *do* anything. *But*, now that our services have this tag, we can
replace the manual service mapping we use in `ButtonRemote`'s
`#[AutowireLocator]` attribute with the tag name.

When you use the `#[AutoconfigureTag]` attribute, you can customize the tag
name. If you choose not to, the interface name will be used by default. We'll
just stick with the default name because the name isn't super important.

Okay, back in our browser, let's see if everything still works. Press a button
and... *error*.

> Button "power" not found.

Scroll down to see the exception details. Ah, here it is!
This "not found" exception was thrown from the `press()` method because it
couldn't find "power" in `ButtonRemote`'s container. *Luckily*, the previous exception
details shows us the
service IDs it *did* find - the *full class names* of our buttons. So they *are*
being wired up, but not by the ID we need.

We want the service ID's of our container to be the *slug* names of our buttons. To
do this, we'll use our next dependency injection attribute: `#[AsTaggedItem]`.
Putting this on the implementations of `ButtonInterface` allows us to customize
the service ID.

Start with the `ChannelDownButton`. Add `#[AsTaggedItem]` to the class...
and for the *first* argument, which is the index, we'll write `channel-down`.
We'll do the same thing
in `ChannelUpButton`... `PowerButton`... `VolumeDownButton`...
and `VolumeUpButton`.

All right, spin back over to your browser... refresh the page... press a button,
and... *it works*! If we look at the profiler, we can see that it's dumping the
correct message for the button.

So now, whenever we want to add a *new* button, we just need to create the
button class, have it *implement* the `ButtonInterface`, and add
the `#[AsTaggedItem]` attribute with a unique button name.

If we want this to show up in our remote UI, we *still* need to add it to our
template. But what if we could do *even* better? What if we didn't have to edit
this file *every* time we add a new button? The `ButtonRemote` knows all of our
button names, so we'll add a method to *list* them. Then, in our template, we'll
loop over *each* of them and render the buttons. To do *that*, we need to use
*another* dependency injection attribute. *That's next*.
