# Decorate a Service with AsDecorator

Okay, so in the last chapter we added our remote interface and two implementations,  
but right now all we've done is aliased `RemoteInterface` to `ButtonRemote`, so we're  
not using our logger yet. So what we need to do now is we want the `LoggerRemote` to  
decorate the `ButtonRemote`, so anytime we want the `ButtonRemote` it actually will  
inject the `LoggerRemote` that wraps our `ButtonRemote`, and this is how service  
decoration works.

So to do this we'll add our next attribute which is `AsDecorator`, and then we need  
to say what we want it to decorate, so we want it to decorate `ButtonRemote`. So now  
this tells Symfony when it builds the container, anytime we ask for we want a  
`ButtonRemote`, which again remember is aliased to `RemoteInterface`, so when Symfony  
requires a `RemoteInterface` it's going to ask for a `ButtonRemote`, and what this is  
going to do is say hey when you're wanting a `ButtonRemote` actually wrap the  
`ButtonRemote` in this `LoggerRemote`, and right now Symfony is smart enough to know  
because we've type-hinted `RemoteInterface` here it knows to automatically inject  
our `ButtonRemote` in here.

So if we now go, so first let's go back to our controller and we're going to add a  
dump here, a `dd`, and we're going to dump the remote, and let's go back to our app  
and see what this looks like. So if we hit refresh we can see we're getting our  
`LoggerRemote` and then the inner property is `ButtonRemote` and then of course we're  
getting our logger. So this is working correctly, so let's go back and remove this  
`dd`, and then back in the app hit refresh, alright this is a good sign it's working,  
so let's hit the channel up, channel up pressed, and now let's go into our profiler  
for the post request that actually submitted that button, and we can see okay  
everything's still working, the channel up button logic is being executed, but now  
let's go to our logs and check this out, we are showing that on the info logging  
level we can see pressing button channel up, pressed button channel up, so our  
logging is working and our service is decorated, and our `ButtonRemote` is decorated  
as expected.

So one thing to note here is there is sort of a sister attribute to `AsDecorator`,  
and this is used in certain circumstances, Symfony won't know how to automatically  
inject the service that this service is decorating, there are certain scenarios, one  
scenario is if we remove the type hint here and go back to our app and hit refresh,  
we get an error, cannot resolve argument remote, basically it's saying we cannot  
auto-wire service `LoggerRemote`, argument inner of method construct has no type  
hint, you should configure its value explicitly. So in this case one solution is to  
use this next dependency injection attribute, `AutowireDecorated`. This tells Symfony  
to inject the decorated service as this property.

We'll go back to our app, hit refresh, and all's good again. Now we should always use  
type hints when possible, so let's remove this `AutowireDecorated` here and re-add  
our type hint which is `RemoteInterface`, and then we'll remove this  
`AutowireDecorated` attribute from our imports. So by and large the  
`AutowireDecorated` attribute won't need to be used as long as you're using type  
hints. There are a few other scenarios where that might be required. One such thing  
is in advanced cases you might actually be injecting multiple `RemoteInterface`s, in  
that case you will need to tell Symfony which one you want as the decorator. That's a  
pretty advanced use case, that's one pretty advanced use case that doesn't apply  
here and 9 times out of 10 I don't think it'll apply, I just wanted to let you know  
that that service exists for that function.

Alright, we have successfully decorated our `ButtonRemote` with a `LoggerRemote` and  
the logging is working, so next we're going to customize the logging. We're going to  
customize the logger channel that's injected here, and we're going to use something  
called named auto-wiring and we're going to look at some of the caveats of using  
named auto-wiring and how they can be fixed, that's next.
