# More Laziness Attributes

The awesome thing about "lazy services" is they require no changes to your
code (as long as services aren't final). But what if our `ParentalControls`
service was from a 3rd-party package *and* was final? We have some options
but they do require our code to be built *laziness-aware*.

In `VolumeUpButton`, replace `#[Lazy]` with `#[AutowireServiceClosure]`. For the
first argument, use `ParentalControls::class`. This will inject a closure that
returns a `ParentalControls` instance when invoked (and it will only be instantiated
*when* invoked).

To help our IDE, add a docblock above the constructor with
`@param \Closure():ParentalControls $parentalControls`.

Now, down in the `press()` method's `if` statement, switch `false` to `true` so
we'll always detect that the volume is too high. Because `$parentalControls`
is a closure, we need to wrap `$this->parentalControls` in braces and invoke
it with `()` before calling `->volumeTooHigh()`.

Check this out! Because we've added the docblock, our IDE provides auto-completion
and enables us to click through (with CMD+click) to the `volumeTooHigh()` method
on `ParentalControls`. Awesome!

Remove the `dump()` here, spin over to our app, refresh, and press the "volume up"
button. Jump into the profiler and you'll see that the `volumeTooHigh()` logic is
being called. Great! The `ParentalControls` service is only instantiated when
the closure is invoked - and we only invoke it when needed.

Let's look at an alternative way to achieve the same thing. In `VolumeUpButton`,
replace `#[AutowireServiceClosure]` with `#[AutowireCallable]`. Keep
`ParentalControls::class` as the first but prefix with `service:`.

`#[AutowireCallable]` also injects a closure, but instead of returning the full
service object, it instantiates the service, calls a single method on it, then
returns the result.

Make this multiline to give us some more room. Add a second argument:
`method: 'volumeTooHigh'`.

When Symfony instantiates a service that uses `#[AutowireCallable]`, by default, it
will instantiate its service. It's an eager beaver! To avoid this, add a third
argument: `lazy: true`. Now, `ParentalControls` will only be instantiated when the
closure is invoked.

In the docblock above, change the closure return type to `void` to match the
return type of `volumeTooHigh()`.

Down in the `press()` method, remove the `->volumeTooHigh()` call. This is now
called by the closure when invoked.

Spin back to the app, refresh, press the "volume up" button, and jump into the profiler.
The `ParentalControls::volumeTooHigh()` logic is still being called. Perfect!

`#[AutowireCallable]` is certainly cool, but for most cases, I prefer using
`#[AutowireServiceClosure]` because:

1. It's lazy by default.
2. More flexible because it returns the full service object.
3. And, with proper docblocks, we get:
  - Auto-completion
  - Method navigation
  - Refactoring support
  - And better static analysis with tools like PhpStan

---

That's it for this course!

YAML and XML service configuration isn't going away. These attributes are about improving
your developer experience by keeping your code *and* service configuration together.

More attributes are being added in almost every new Symfony version. Follow the [Symfony
blog](https://symfony.com/blog) to stay up-to-date! Check this out, in Symfony 7.2, there's
a new `#[WhenNot]` attribute! It's basically the opposite of the `#[When]` attribute we
discussed earlier. Cool!

Check out the "Dependency Injection" section of the
[Symfony Attributes Overview](https://symfony.com/doc/current/reference/attributes.html#dependency-injection)
doc to see a list of all the dependency injection attributes that are currently available
and how they work.

'Til next time! Happy coding!
