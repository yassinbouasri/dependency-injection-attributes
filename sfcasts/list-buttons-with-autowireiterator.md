# List Buttons with AutowireIterator

Now that we've successfully refactored our app with a controller that uses the
Command pattern to execute buttons, let's look at how we can change this
UI from hard-coded buttons to something more *dynamic*. As we add new
button classes, I'd like to not have to edit our template.

The first thing we need to do is open our `ButtonRemote` class. We need to list
*all* of our button names - the indexes from our container. The best way to do this is to
create a `public` method here called `buttons()`, which will return an `array`.
Above, we'll add some documentation showing that this is an array of
strings. These are going to be our button names.

This container is great for fetching services, but it can't
iterate over the concrete buttons inside. To fix that, we need to
change `#[AutowireLocator]` to a new attribute called `#[AutowireIterator]`.
When we do this, we're telling it to inject a list of our services, so this will no longer
be a container interface. That means we can just typehint `iterable` and rename
this container to `$buttons` here... and here. Nice!

Now, down here, we're going to loop over the buttons with a `foreach`, so
write `foreach ($this->buttons as $name => $button)`. `$button` is the actual service.
We're going to ignore that *completely* and just grab the `$name`, and add it to
this `$buttons` array. Then, down here, `return $buttons`.

Back in our controller, we're *already* injecting the `ButtonRemote`, so down
here where we render the template, inject the buttons
with `'buttons' => $remote->buttons()`. To see what this is going to return,
we'll add a `dd()` here.

Okay, back at our browser, refresh the page and... hm... we're not seeing what
we want to see here. This is just a list of numbers, but we *want* this to be
a list of button *names*. To fix this, back in `ButtonRemote`,
find `#[AutowireIterator]`. The `#[AutowireLocator]` automatically uses the `$index`
property from the `#[AsTaggedItem]` attribute but
for `#[AutowireIterator]`, when it injects this `iterable`, it's just a list of
services (keyed by integers).

We need to tell it to use `#[AsTaggedItem]`'s `$index` as the _key_, so in our
`#[AutowireIterator]`, add another parameter called `indexAttribute`. This will
be `key`. Now, when we loop over `$this->buttons`, `$name` will be the `$index`
which in our case, is the button name.

Over in our controller, we still have this `dd()` so, back in our app, refresh
and... there we go! We have the actual button names now! We can loop over
them in our template and render each button programmatically. Pretty cool!

Go ahead and remove this `dd()`.

Back in our editor, open `index.html.twig`. Right here, you can see we're
creating an unordered list. That's where each button lives right now. To avoid
hard-coding them like this, we're going to loop over the buttons that we're
passing to our template. *Then* we're going to
grab the buttons in our template. Add some space here, and then
write a for-each loop: `for button in buttons`.

In our UI, you may have noticed that the *first* button - the "Power"
button - looks a little different. It's red and larger than the others. To keep
it the way it is, with the first button being different, we'll utilize a special
Twig variable called `loop`. Specifically, `loop.first` which is `true` if it's
the first iteration of this `foreach`. So let's add an `if loop.first` here.
Below, we'll add an `else` for the rest of the buttons.

We'll copy the _code_ for the first button and _paste_ it here. Instead of
hard-coding "power" as the button's value, we'll render the `button` variable here.
Same for the Twig icon's name. 

For the rest of the buttons, we'll _copy_ the first standard button's code and _paste_
it here. As we did for the first button, replace the button's _value_ attribute
and the icon's _name_ with the `button` variable.

*Nice*. Now we can delete the rest of the hard-coded buttons.

That simplifies our template *quite a bit*. If we spin back over to our app and
refresh... hm... It's rendering the buttons, but they're not in the right order.
We want this one at the top. So... what do we do?

We need to *enforce* the order our buttons are injected into the iterator. To do
that, open `PowerButton.php`. In `#[AsTaggedItem]`, there's another argument
we can use called `priority`. That will enable us to *order* the iterator's services.

When we were using `#[AutowireLocator]`, this wasn't important because we were just
fetching services by their index. But as soon as we start iterating *over* things, we may want to
control the order. Add `priority` and set it to `50`, because a higher priority
means it will move up the chain. We're assigning a priority of `50` for the "Power"
button because we want it to be first. Now we can go to the "Channel Up"
button and add a priority of `40`, the "Channel Down" button, a priority
of `30`, "Volume Up" a priority of `20`, and "Volume Down", a priority of `10`.

Any button *without* an assigned priority has a default priority of `0` and
no guarantee of order.

If we head back to our app and refresh... *all right*! We're back in business! All
of our the buttons are being added *programmatically* to our template in the proper
order.

*But* you may have noticed we have a *tiny* problem here. Let's *press* a
button and... Error!

> Attempted to call an undefined method "get" of class
> "Symfony\Component\DependencyInjection\Argument\RewindableGenerator".

This `RewindableGenerator` is a special iterator Symfony injects when using `#[AutowireIterator]`.
The *problem* originated when we
refactored to use `#[AutowireIterator]`. We no longer have that container, so
it's calling `get()` on something that doesn't have a `get()` method.

Next, let's fix this and get our remote working again.
