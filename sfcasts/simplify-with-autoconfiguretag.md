# Simplify with `#[AutoconfigureTag]` and `#[AsTaggedItem]`

Okay, welcome back. So in the last chapter, we refactored the big switch statement that
we had in our controller to this little try-catch. Our remote works the same way it did
before, but we've refactored it to the command pattern.

We used this `AutowireLocator` attribute to tell Symfony how to wire up this
container. This is great and totally valid. But we can simplify this even more!

Because all of our buttons implement this `ButtonInterface`, we'll add our second
dependency injection attribute: `#[AutoconfigureTag]`. This will add a _tag_ to all services that
implement `ButtonInterface`. This attribute tells Symfony, "Hey, add a tag to any service
that implements this interface.". A tag is just a string that's connected to a service.
By itself, it doesn't do anything. But now, because they all have this tag, we can remove
replace the manual service mapping we use in `ButtonRemote`'s `#[AutowireLocator]` attribute
with the tag name.

When adding the `#[AutoconfigureTag]` attribute, you can customize the tag name. But if you
don't, the interface name will be used. I'm just going to use the default
because the name of the tag is not terribly important. So we can remove this array here and
simply replace with `ButtonInterface::class`.

So back in our browser, let's see if everything still works. Press a button and... an error.

> Button "power" not found.

Let's scroll down a bit and see if we can find the previous exception. Ah, here it is!
This not found exception was thrown from our `press()` method because it couldn't find
"power" in our `ButtonRemote`'s container. Luckily, it shows us the service IDs it did find: the full
class names of our buttons. So they're being wired up but not by the ID we require.

## Anatomy of a Service Tag

As stated earlier, a service tag is just a string but it also can have an optional set
of attributes. These attributes can be anything but there are a few reserved ones that
Symfony uses. One of which is `index`. When Symfony wires up a container with a tag, it
will use the `index` attribute as the ID of the service. If no `index` is provided, the
service ID will be the fully qualified class name of the service. This is what we're seeing
in this exception message.

We want the `index` to be the _slug_ name of our buttons. To do this, we'll use our third
dependency injection attribute: `#[AsTaggedItem]`. Putting this on the _implementations_
of `ButtonInterface` allows us to customize the `index` attribute.

Let's start with `ChannelDownButton`. We'll add `#[AsTaggedItem]` to the class and
for the first argument, which is the index, we'll write `channel-down`. We'll do the 
same thing in `ChannelUpButton`, `PowerButton`, `VolumeDownButton`, and `VolumeUpButton`.

Let's spin back to our browser, refresh the page, press a button, and... it works! If we
look at our profile, we can see that it's dumping the proper message for the button.

So now, whenever we want to add a new button, we just need to create the button class,
have it implement the `ButtonInterface`, and add the `#[AsTaggedItem]` attribute with
a unique button name.

To actually have it show up in our remote UI, we still need to also add it to our template.
But I think we can do better. It would be nice if we didn't have to edit this file every
time we add a new button. Our ButtonRemote knows all our button names, so let's add a
method to list them. Then in our template, we'll loop over each of them and render the
buttons. To do this, we'll need to use another dependency injection attribute. Let's
look at that next!
