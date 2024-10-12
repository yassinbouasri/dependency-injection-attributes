# Decorate a Service with AsDecorator

In the last chapter, we added a remote interface, two implementations, and aliased `RemoteInterface` to `ButtonRemote`. That's awesome, but we still have more work to do - we're not using our logger yet. We need the `LoggerRemote` to *decorate* `ButtonRemote`, so anytime we use `ButtonRemote`, it will *inject* the `LoggerRemote` that wraps our `ButtonRemote`. This is how service decoration works.

To get started, we need to add a new attribute - `#[AsDecorator]` - and tell it what we want it to decorate: `ButtonRemote`. When Symfony requires a `RemoteInterface`, it's going to ask for `ButtonRemote`, which basically says:

`Hey! When you need "ButtonRemote", wrap the
"ButtonRemote" in this "LoggerRemote".`

Since we type hinted `RemoteInterface`, Symfony is smart enough to inject our `ButtonRemote` *automatically*.

Over in our controller, add `dd($remote)`... and, back in our app, let's wee what this looks like. Refresh, and... we're getting our `LoggerRemote`, the `inner` property is `ButtonRemote`, and we're also getting our logger. So this is working correctly! Go back and remove this `dd(0)`, refresh our app again, and... good! It seems like it's working. If we press the "Channel Up" button... "Channel up pressed". Good! And if we open the profiler for the `POST` request that *submitted* the button... we can see that everything's still working. The "Channel Up" button logic's being executed. To be *super* sure this is working like it should, let's check "Logs", and... yep! On the "info" logging level, we see "Pressing button channel-up" and "Pressed button channel-up". That means logging is working and our `ButtonRemote` is decorated as expected. *Nice*!

Okay, something to note here is a sort of *sister* attribute to `#[AsDecorator]` called `#[AutowireDecorator]`, which is only used in certain circumstances. For example, if we remove the type hint here, go back to our app and refresh... we get an *error*:

`Cannot resolve argument $remote of "App\Controller\RemoteController::index()": Cannot autowire service "App\Remote\LoggerRemote": argument "$inner" of method "__construct()" has no type-hint, you should configure its value explicity.`

In this *specific* case, we can use `#[AutowireDecorated]` to tell Symfony to inject the *decorated* service as this property. If we go back to our app and refresh... back to normal!

We should always use type hints when possible, so let's remove `#[AutowireDecorated]` here and re-add our type hint, `RemoteInterface`. We can also remove this `#[AutowireDecorated]` attribute from our imports. We'll rarely need to use this attribute long as we're using type hints, but there *are* a few *other* scenarios where that might be required.

In advanced use cases, we may be injecting *multiple* `RemoteInterface`s. In *that* situation, we would need to tell Symfony which one we want to be the decorator. It's pretty rare and we won't do that here, but I wanted you to know that it's possible.

All right! We've *successfully* decorated our `ButtonRemote` with a `LoggerRemote` and the logging is *working*. Next, we're going to customize the logger channel that's injected using *named autowiring*. We'll also look at some of the *problems* with named autowiring and how to fix them.
