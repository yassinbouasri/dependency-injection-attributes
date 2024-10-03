# List Buttons with AutowireIterator

Now that we've successfully refactored our app with a controller that uses the Command pattern to execute buttons, let's look at how we can change this interface from hard-coded buttons to something more *dynamic*, so as we add new buttons, we don't have to jump into our template and edit it.

The first thing we need to do is open our `ButtonRemote` class. We need to list *all* of our buttons here *and* their service IDs. The best way to do this is to create a `public` method here called `buttons()`, which will return an `array`. Below that, we'll add some documentation showing that this is an array of strings. These are going to be our button IDs.

The `#[AutowireLocator]` is connected to this container, and our container can't iterate over the concrete buttons inside. To fix that, we need to change `#[AutowireLocator]` to a new attribute called `#[AutowireIterator]`. When we do this, we're telling it to inject an iterator, so this will no longer be a container interface. That means we can just typehint `iterable` and rename this container to `$buttons` here... and here. Nice!

Now, down here, we're going to loop over the buttons with a `foreach`, so say `foreach ($this->buttons as $name => $button)`. That's the actual service. We're going to ignore that *completely*, grab the `$name`, and add it to this `$buttons` array. Then, down here, `return $buttons`.

Back in our controller, we're *already* injecting the `ButtonRemote`, so down here where we render the template, inject the buttons with `'buttons' => $remote->buttons()`. To see what this is going to return, we'll add a `dd()` here.

Okay, back at our browser, refresh the page and... hm... we're not seeing what we want to see here. This is just a list of numbers, but we *want* this to be the button *names*. To fix this, back in `ButtonRemote`, find `#[AutowireIterator]`. The `#[AutowireLocator]` automatically uses the tag attribute we added here as the *ID* for the locator. But for `#[AutowireIterator]`, when it injects this `iterable`, it's keyed by integers like the order it comes in.

We need to tell it to use our key attribute as the index, so let's add a second one called `indexAttribute`, which will index by `key`. And *this* will be the *key* for each button.

Over in our controller, we still have this `dd()` so, back in our app, refresh and... there we go! We have the actual button names now! These are the IDs within the container, and *now* they're the IDs of the buttons. We can loop over them in our template and render each button programmatically. Pretty cool!

Back in our editor, open `index.html.twig`. Right here, you can see we're creating an unordered list. That's where each button lives right now. To avoid hard-coding them like this, we're going to loop over the buttons that we're passing to our template. Go ahead and remove this `dd()`. *Then* we're going to grab the buttons in our template. Add some space here, and then say `{% for button in buttons %}`.

In our front end, you may have noticed that the *first* button - the "Power" button - looks a little different. It's red and larger than the others. To keep it the way it is, we'll just copy this line here and utilize an `if loop` with a special Twig variable - `first`. So this will be true if it's the *first* iteration of this `foreach`. Below, we'll add an `else`. So when it's the *first* iteration, this will render the button so it looks a little different than the other ones. We'll render the rest of the buttons *normally*. *So*, copy this because it's the first button, and... *paste*.

Now we just need to edit this a bit. The name is `button`, and instead of hard-coding "power", we'll add `button` here. Now we're going to render the button from our loop. This is the *name* of the button, and that should match our slug, which is the service ID in our container.

We'll do the same thing where we're using this icon here. Instead of hard-coding `power`, we'll use `button`. That will render the proper SVG file for this button. Now we'll go down here and grab the *standard* button template, and back up in the `else`... *paste*. Just like we did before, let's render the `button` variable as the *value* and the *name* as the icon. *Nice*. Now we can delete the rest of the hard-coded buttons. 

That simplifies our template *quite a bit*. If we spin back over to our app and refresh... hm... It's rendering the buttons, but they're not in the right order. We want this one at the top. So... what do we do?

We need to *enforce* the order our buttons are injected into the iterator. To do that, open `PowerButton.php`. In `#[AsTagItem]`, there's another tag attribute we can use called `priority`. That will enable us to *order* things and decide how they'll be injected.

When we were using `AutowireLocator`, this wasn't important because we weren't iterating. But as soon as we start iterating *over* things, we may want to control the order. Add `priority` and call it `50`, because a higher priority means it will move up the chain. We're assigning a priority of `50` for the "Power" button because we want it to be first. Now we can go to the "Channel Up" button and add a priority of `40`, the "Channel Down" button a priority of `30`, "Volume Up" a priority of `20`, and "Volume Down" a priority of `10`.

Any button *without* an assigned priority has a priority of `0` by default. That also means that we won't have control over the *order* in our iterable list. If we head back to our app and refresh... *all right*! We're back in business! All of our the buttons are being added *programmatically* to our template.

*But* you may have noticed we have a *tiny* problem here. Let's *press* a button.

`Attempted to call an undefined method "get" of class
"Symfony\Component\DependencyInjection\Argument\
RewindableGenerator".`

This `RewindableGenerator` is the iterator Symfony injects. It's a special wrapper around a traversable, iterable element. The *problem* originated when we refactored to use `#[AutowireIterator]`. We no longer have that container, so it's calling `get()` on something that doesn't have a `get()` method.

Up next: Let's fix this and get our remote working again.
