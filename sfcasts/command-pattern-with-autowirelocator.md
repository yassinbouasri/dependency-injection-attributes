# Command Pattern with AutowireLocator

Okay, let's get started. So looking at our app, this is our remote UI. It’s basically just a form, and each button
on the submits the form. Each button's `name` attribute is unique so our controller can determine which
button logic to execute. So you can see when we click the power button here, we see this flash message. This is added by the
controller and tells us what happened. For example, the power button was pressed, then channel up was pressed, and channel
down was pressed.

This form just posts to the same page, handles the button logic, and then redirects us back with the flash message. We can
look in the profiler to see what that post request looked like by clicking here. You can see the last button we pressed was
the channel down button. Each button has a `dump()` message that we're using to represent the _button logic_.

We can go into `src/Controller`, find the `RemoteController`, and here we go. We’re checking if the request is a `POST`, and
then we have a switch statement where we grab the button name from the request. Each button submits with a name, and we
handle this for each button with a case for power, channel up, etc. These `dump()`'s represent the logic for each
button. If we can’t find the button, we throw a 404 with a little message. Then we add the flash message, doing a bit of
string manipulation to make the button name look nicer, and redirect right back to the same route.

At the bottom, if the request isn’t a post, we just render our `index.html.twig`, which is our remote template. When you have a big
switch-case statement like this, it’s a good opportunity to refactor, especially as you add more buttons and logic. A great
refactor for this is to use the command pattern. If you want a more in depth look at this pattern, 
search for "Command Pattern" on SymfonyCasts. You'll find our full "design patterns" course.

The first thing we’re going to do is create our commands, which will represent the buttons and house all their logic. We’ll
create a new directory in our `src/` folder to organize our code a bit. We’ll call this `Remote`, and inside, create a
subfolder called `Button`. Then we’ll create a new PHP class for each button. We’re going to start by creating an interface
that each button will need to implement so that our command handler can handle these buttons predictably. We’ll call it
`ButtonInterface`. You can see that PHPStorm automatically creates an interface for us. We’ll add one public method, `press`,
which will have no arguments and return `void`.

For the actual button implementations, I already have them created in the `tutorial/` directory. You can just copy all the
`PHP` files into this `Button` directory. Each button's `press()` method, has the same `dump()` we saw in our controller. `press` method. 
For example, the `ChannelDownButton` has the `Change channel down` message.

We now have our commands, which are the buttons. Next, we need a command handler to take the button
name and execute the corresponding command. For this, we’ll create an object to act as our command handler. Under
`Remote`, we’ll create a new class called `ButtonRemote`. I like to mark classes as `final` by default, removing it only if
extension is needed. This is just a personal preference, so feel free to leave `final` off.

In this class, we’ll create a public method called `press()`, which will take a string argument `$name`, representing the button
name. This method has no return so use `void` as the return type. We’ll now create a constructor for this object, which will take a `ContainerInterface`.
We’ll grab this from `PSR\Container`. This container will store our button objects as a key-value store, with the key
being the button name and the value being the button object. In the `press()` method, we’ll use `$this->container->get($name)` to
retrieve the `ButtonInterface` instance and then call `press()`.

At this point, calling the `press()` method will give us an error because Symfony doesn’t know how to wire up this container.
To inform Symfony, we’ll use the `AutowireLocator` dependency injection attribute. In older Symfony versions, this was called
`TaggedLocator`, but it was renamed in Symfony 7.1 to be more consistent with other attributes. The first argument on the
attribute will be an array with the button names as keys and their corresponding class names as values. Symfony will convert these into the
actual button instances when building the container.

Let’s go ahead and add all our buttons to this container: `Power`, `ChannelUp`, `ChannelDown`, `VolumeUp`, `VolumeDown`. 
Our command handler is ready! Now, we need to replace the big switch-case statement in our controller with this.

Back in the controller, after injecting the `Request`, let’s inject `ButtonRemote`. Since this controller is autowired,
Symfony will automatically inject this. We’ll copy this code to get the button name, then call `press()` on the
injected `ButtonRemote` object with the button name. We can now remove the entire switch statement, but we still need to handle
cases where a button isn’t found. We’ll handle the 404 by wrapping `press()` in a `try-catch` block, catching a
`NotFoundExceptionInterface`. If `ContainerInterface::get()` doesn’t find a command for the button name,
it will throw this exception.

We’ll add the caught exception to the 404 for better debugging. Our controller is now much smaller, so let’s test it in
our app. Press the power button: “Power pressed.” Channel up: “Channel up pressed.” Everything seems to be working. If we
look into the profiler, we can see the `dump()` message is still there and now comes from the correct button implementation.

There’s another improvement we can make. Every time we add a new button, we currently need to update the `AutowireLocator` attribute
in our `ButtonRemote`. This is valid, but a bit cumbersome. In the next chapter, we’ll explore a refactor to remove this requirement. That’s next.
