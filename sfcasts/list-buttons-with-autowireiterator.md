# List buttons with `#[AutowireIterator]`

Okay, now that we've successfully refactored our app to use our controller to
use the command pattern for executing the buttons, let's look at how we can
change this interface from being hard-coded for each button in our template to
being dynamic. So as we add new buttons, we don't have to jump into our
template and edit it.

The first thing we're going to do is go into our `ButtonRemote` class, and we
are going to first change... So we need to list all the buttons and their IDs,
their service IDs. So I think what we're going to do is create a public method
here called `buttons`, and this is going to return an array. We'll add some doc
blocks here to just say it's going to be an array of strings. These will be the
button IDs.

The `AutowireLocator` is for a container, and our container cannot iterate over
the buttons that are in it, over the concrete buttons that are in it. So what
we want to do is change this to `AutowireIterator`, a new attribute called
`AutowireIterator`. When you do this, it's no longer going to be a container
interface. What this tells it is to inject an iterator. So we can just type
hint this to `iterable`, and then let's just rename this container now to just
`buttons`.

Then down here, we're going to loop over the buttons. So we'll create an array
and then we'll do a `foreach`. The buttons will be keyed by the button ID, so
we'll call it `name` and `button`. That will be the actual service. Now we're
going to completely ignore the service and we're just going to grab the name
and add it to this buttons array. Then down here, we're just going to return
`buttons`.

Let's hop back to our controller, and we're already injecting the `ButtonRemote`.
So down here where we render the template, we're going to just inject the
`buttons`, `Remote::buttons`. Now to see what this `buttons` is going to return,
let's just `dd` it here.

Jump back to our app and refresh the page to see what we get. Okay, so we're
not seeing what we want to see here. We're seeing just a list. We want this to
be the button names. To fix this, let's go back to our `ButtonRemote`. If we go
back up to this `AutowireIterator`, for the `AutowireLocator`, it automatically
uses our tag attribute that we added here as the key or as the ID for the
locator. But for the `AutowireIterator`, when it injects this `iterable`, it
keys it by integers, like the order that it comes in.

What we need to do is tell it to use our attribute as the index, our key
attribute as the index. So for this, we'll add a second one called
`indexAttribute`. We're going to ask it to index by `key`. The key is the
default, the key will be what is used. This should be the key for each one.

If we go back to our controller, we still have that `dd`. So let's go back to
our app and refresh. There we go. Now we have the actual button names. These
are the IDs within the container. These are now the IDs of the button, and we
can loop over them in our template and render each button programmatically.

Let's grab our `index.html.twig`. Right here, we are creating an unordered
list, so that's where each button is. Instead of hard coding them, we're going
to loop over the buttons that we're passing to our template. First, let's
remove this `dd`. We're going to grab these buttons in our template. Let's add
some space here, and then we're going to do `for button in buttons`.

If you notice in our front end, the first button is bigger and red for the
power button. So we're just going to copy this line here. We'll do an `if` with
this loop special Twig variable `first`. This will be true if it's the first
iteration of this `foreach`. Then we'll add an `else`. When it's the first
iteration, we want to render the button a little bit differently so it looks
different than the other ones.

For the rest, we're just going to render them as normal buttons. Let's copy
this, because that's the first button, and paste. Then we just need to edit
this a bit. The name is `button`, and instead of hard coding power, we're going
to put `button` here. We're going to render the button from our loop. This is
the name of the button that should match our slug, which is the service ID in
our container.

Same here where we're using this icon, instead of hard coding power, we'll put
`button` here. This will render the proper SVG file for this icon. It'll render
the proper SVG for this button. Now we'll go down here and cut this line. This
is a standard norm. Now we'll go down here and grab the second button, which is
just on the standard button template. This will just be the standard button
look. Then we'll go up here, in the `else`, and paste.

Same as above, we're just going to render the `button` variable as the value
and the name of this icon button. There we go. Now we can delete all the rest
of the hard-coded buttons. Okay, that simplifies this template quite a bit.
Let's spin back over to our app and refresh.

Okay, it's rendering the buttons, but they're not in the right order. We want
this up top. Yeah, things are just not ordered correctly. What we need to do is
enforce the order at which these are injected into the iterator. We can do that
by opening up our buttons. In this `addTag` item, there's another tag attribute
called `priority`. Priority is a way that you can order things and how they're
going to be injected.

When we had the `AutowireLocator`, this wasn't important because we weren't
iterating. But as soon as you start iterating over things, you might want to
control the order. We'll add a priority and call it `50` because a higher
priority means it'll be moved up the chain. We're going to do a priority `50`
for the power button because we want that to be first. Then we'll go to the
channel up button and add a priority of `40`, channel down, priority of `30`,
volume up, priority of `20`, volume down, priority of `10`.

If you don't add a priority, it is considered priority `0`, and then you don't
have control over the order in your iterable list. Now if we hop back to our
app and refresh, all right, we're back in business here. Now we have all the
buttons being added programmatically to our template.

But you might've noticed that we have a little bit of a problem here. Let's
check this out. Let's press the button and check this out. Attempted to call
undefined method `get` of class `RewindableGenerator`. First, `RewindableGenerator`
is the iterator that Symfony injects. It's a special wrapper around a
traversable, iterable element.

The problem here is when we refactored to use `AutowireIterator`, we no longer
have that container. So this is the failure here. It's calling `get` on something
that doesn't have a `get` method because it's no longer a container. In the next
chapter, we'll fix this and get our app working again.


