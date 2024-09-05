# Alias an Interface with AsAlias

Alright so now that we've successfully refactored our buttons to make it much  
easier to add new buttons and have them automatically listed in our remote, let's  
look at now adding features. So let's look at now adding a feature. So I think it  
would be good in our button remote to add some logging. So before and after a  
button press, we will log, we will add a little log message. So we could inject our  
logger here, the Symfony logger here, which is from the Symfony logger here, but  
that would technically violate the single responsibility principle of this class.  
And what that means is it would mean the class is doing too much. So already we're,  
you know, clicking the proper button here and running the button logic. If we add  
logging here, technically that's doing something unrelated to pressing a button. So  
what we're going to do instead is we're going to decorate this button remote with a  
new remote called logger, a new logger remote that is purely responsible for doing  
the logging and then calling the logic of the button remote inside it, but it keeps  
it separate. So the button pressing logic will still be in this and the logging  
remote will only care about logging.

So when using decoration, you could technically create another class, a logger  
remote class that has the same API here, and that will work, but usually it's best  
practice to add an interface and use decoration with interfaces. So the first thing  
we're going to do is we're going to create a remote interface. So in our remote  
folder, we'll create a new PHP class and we'll call this `RemoteInterface`. And  
we're going to add our two methods from `ButtonRemote` in here. So we'll just copy  
them. So we'll grab the press, our two public methods from here. So we'll copy them  
and paste. So we have our press. And then down here, we also get our list of  
buttons. So we'll copy the whole doc block here and then add a semicolon.

And yeah, so this interface is done. So the first thing we'll do is implement the  
interface here in our `ButtonRemote`. Perfect. And PHPStorm seems to be happy. We're  
implementing the interface successfully. So now what we're going to do is we're  
actually going to inject in our controller, instead of injecting the button remote,  
we're going to inject the interface. Typically, Symfony allows auto-wiring concrete  
classes, as well as interfaces. And technically, if possible, but when possible, you  
should defer to using interfaces as it just makes your code easier to extend later  
on. So if we go to our controller, and right here, instead of `ButtonRemote`, we're  
going to do `RemoteInterface`. We're going to change this type hint to  
`RemoteInterface`.

Now let's jump back to our app and make sure everything's still working. So refresh.  
And great, looks like it's all still working. So now what we want to do is add a new  
implementation of `RemoteInterface` for the logger. So we'll add a logger button, a  
`LoggerRemote`. All right, and we'll implement this interface, our  
`RemoteInterface`. And then we'll use `Ctrl-Enter` here to implement the methods.

All right, so what this service needs is it needs the logger, of course, but what it  
also needs because we're using service decoration, we need to also inject another  
instance of `RemoteInterface`, the one that we're decorating. So we'll add a  
constructor here, and we'll just clean this up a little bit.

And then, so first thing we're going to do is inject our `LoggerInterface`, the one  
from PSR log, `Logger`. And then we're going to inject `RemoteInterface`. So just  
like in our controller, it's always best if possible to inject the interface as  
opposed to the concrete class. We could inject an instance of `ButtonRemote` here,  
but let's use the interface to follow sort of best practices.

So the first thing we want to do is basically buttons. We want to defer to our  
decorated service. We're not going to do any logging here. So let's just call this,  
this remote. Actually, you know what, what we're going to do is I'm going to rename  
this inner. So it's super clear that this is the inner service that we're  
decorating. So we'll call this `innerButtons` and we'll return this. Okay. So we're  
done here. We'll do the same thing here. We will call this `innerPress` and then  
pass the name.

So now this decorator is deferring all the calls to the inner service that it's  
decorating, but now we want to add our logging. So we will, before we press the  
button, we'll add this logger. We'll do, we'll log to the info type, and then we'll  
just do `pressing button`, and then we'll do `{{ name }}`. And we will add a context  
array here and we will pass `name`.

So what this is doing is this is a little syntax that monolog uses, which is the  
default Symfony logger, and it allows you to inject in this, it's sort of like a  
little templating string to inject your context variables into the string, just  
saves you from having to use like a `sprintf` or something like that. So we'll copy  
this and paste it after we execute the button press. And instead of pressing, we'll  
call `pressed`.

Okay. So let's go back to our app and see if everything's still working. So we'll  
refresh and uh-oh, an error. So our controller requires a remote argument that could  
not be resolved. Cannot auto-wire argument remote. It references interface  
`RemoteInterface`, but no service exists. So what's happening here is we now have  
our interface now has two implementations. There's a `ButtonRemote` implementation  
and a `LoggerRemote` implementation.

And basically what this error is telling us is Symfony doesn't know which one to use.  
When we only had our `ButtonRemote`, because there's only one service that  
implemented the `RemoteInterface`, Symfony knew to just use that, but now it  
doesn't know. And if we look back at the error message, it gives us a little hint.  
You should maybe alias this interface with one of these existing services,  
`ButtonRemote`, or `LoggerRemote`. So what we need here is an alias.

An alias in Symfony allows you to, for our purposes allows us to say, when someone  
asks for this interface, this `RemoteInterface`, what service should be used and  
that will solve this problem. So the way to do that is we're going to use our next  
dependency injection attribute, and this one's called `asAlias`. So what you need to  
do for this is find the service that you want to be aliased with `RemoteInterface`  
and make sure that it does implement `RemoteInterface`.

And at the top here, we can add the attribute `asAlias`. And now what this does is  
it tells Symfony that anytime we ask for one of the interfaces that this service  
implements, in our case, just `RemoteInterface`, inject this one. So if we go back  
to our app and refresh, we should be good to go. Perfect. It's working.

One thing we don't have now is logging. We added our `LoggerRemote`, but logging  
isn't working yet. Right now, it's basically just ignored. The service isn't used.  
So in the next chapter, we'll look at how we can decorate `ButtonRemote` with  
`LoggerRemote` and actually make this all work together. That's next.
