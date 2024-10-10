# List Buttons with AutowireIterator

We've refactored our app to use the
Command pattern to execute each button. Great! New goal: make the buttons more
dynamic: as we add new button classes, I'd like to *not* have to edit our template.

Start inside `ButtonRemote`. We need to way to get a list of
*all* the button names: the indexes from our container. To do that,
create a `public` method here called `buttons()`, which will return an `array`.
This will be an array of strings: our button names!

The mini-container is great for fetching individual services. But you can't
loop over *all* the button services inside. To fix that,
change `#[AutowireLocator]` to `#[AutowireIterator]`.
This tells Symfony it to inject an *iterable* of our services, so this will no longer
be a `ContainerInterface`. Instead, used `iterable` and rename
`$container` to `$buttons` here... and here. Nice!

Now, below, loop over the buttons:
`foreach ($this->buttons as $name => $button)`. `$button` is the actual service,
but we're going to ignore that *completely* and just grab the `$name`, and add it to
this `$buttons` array. At the bottom, `return $buttons`.

Back in the controller, we're *already* injecting `ButtonRemote`, so down
where we render the template, pass a new `buttons` variable
with `'buttons' => $remote->buttons()`. Add a `dd()` to see what it returns.

Okay, back at the browser, refresh the page and... hm... that's not quite what
we want. Instead of a list of numbers, we want
a list of button *names*. To fix this, back in `ButtonRemote`,
find `#[AutowireIterator]`. `#[AutowireLocator]`, the attribute we had before,
automatically uses the `$index`
property from `#[AsTaggedItem]` for the service keys.
`#[AutowireIterator]` does not! It just gives us an iterable with *index* keys.

To tell it to key the iterable using `#[AsTaggedItem]`'s `$index`, add
`indexAttribute` set to
`key`. Now, when we loop over `$this->buttons`, `$name` will be the `$index`
which in our case, is the button name.

Over in our controller, we still have this `dd()` so, back in our app, refresh
and... there we go! We have the button names now! Pretty cool!

Remove the `dd()`, then open
`index.html.twig`. Right here, we have a hardcoded list of buttons.
Add some space, and then
`for button in buttons`.

In the UI, you probably noticed that the *first* button - the "Power"
button - looks a different: it's red & larger. To keep
that special styling,
add a `if loop.first` here, an `else` then the rest of the buttons.

Copy the code for the first button and paste it here. Instead of
hard-coding "power" as the button's value, render the `button` variable.
Same for the Twig icon's name.

For the rest of the buttons, copy the first button's code, paste, then replace
the button's `value` attribute
and icon name with the `button` variable.

*Nice*. Celebrate by deleting the rest of the hard-coded buttons.

Let's try it! Spin back over to our app and
refresh... hm... It's rendering the buttons, but they're not in the right order.
We want this one at the top. So... what do we do?

We need to *enforce* the order of our buttons in the iterator. To do
that, open `PowerButton`. In `#[AsTaggedItem]`, add
`priority`.

Before, with `#[AutowireLocator]`, this wasn't important because we were just
fetching services by their name. But now that we *do* care about
the order, add `priority` and set it to, how about, `50`.
Now we go to the "Channel Up"
button and add a priority of `40`, the "Channel Down" button, a priority
of `30`, "Volume Up" a priority of `20`, and "Volume Down", a priority of `10`.

Any button *without* an assigned priority has a default priority of `0` and
no guarantee of order.

Head back to our app and refresh... *all right*! We're back in business! All
the buttons added *automatically* and in the right
order.

*But* you may have noticed we have a *tiny* problem. Press any
button and... Error!

> Attempted to call an undefined method "get" of class
> `RewindableGenerator`.

This `RewindableGenerator` is the iterable object Symfony injects with `#[AutowireIterator]`.
We can loop over this, but it does *not* have a `get()` method. Boo!

Next, let's fix this by also injecting a service locator via,
of course, another fancy attribute!
