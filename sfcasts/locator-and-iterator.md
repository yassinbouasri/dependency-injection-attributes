# Locator and Iterator with `ServiceCollectionInterface`

All right, so in the last chapter, we added these buttons programmatically from our
remote but we broke the functionality of actually pressing the buttons because we are
no longer injecting a container. So if we open our button remote, there's a couple
ways we could solve this. Number one, which is maybe the easiest, is we could inject
two, two arguments here. One that is an iterator and one for the container. And that
would work and be perfectly valid, but there is a better way. There is a way that we
can inject, we can inject something that is both an iterator and a container. And
what this, and what you want to inject here is called a service collection interface.
And then let's take a look at this, at this interface. So this just extends a service
provider interface. And we'll look at that in a second, but you can see here, it's an
iterator aggregate. So it, in addition to, and it's also countable. So we can count
the services in there if that was a requirement. So if we look in service provider
interface, you can see this just extends container interface. So this is essentially
both a container. So this is essentially both a container and an iterator at the same
time. So it saves us from having to inject two different things. And then now,
instead of injecting, we're going to switch this one back to autowire locator. Now
it's being injected. Now this will be injected and be both an iterator and a service
container. Let's just clean up some unused imports here. And now this code should
work as expected because we'll be able to both loop over all the buttons within the
iterator. And still being able to get and press each button through the container. So
let's just refresh our app. Let's just refresh our app. And OK, we're still listing
the buttons. So that's a good sign. And this is working again. And if we jump back
into our profiler and look at this, the post request, we can see we're still calling
our logic in the channel down button. Based on the button found via our command
pattern. OK. So one thing we need to talk about a little bit is laziness. So one
great thing about the service container being injected is the underlying button
services are not instantiated until you call get on it. So if we call get on power,
only the power button will be instantiated in our app and the other ones won't be. So
this is a really nice way to improve performance because you don't have to
instantiate every button. But we have a little bit of a problem here. As soon as we
start iterating over the buttons, we're actually instantiating them. So we've kind of
removed the laziness and now we're instantiating each button. For our little app,
it's not a big deal. But you can imagine if we had hundreds of services in here, this
would not be a good solution. So the way so we can now we can have the service
collection interface is can come to the rescue here. So we're going to remove all
this code and let's dd this buttons get provided services. Let's jump back to our
browser and see what that looks like. Okay, so we can see this looks very similar to
what we had back in chapter two. So we can see that this method get provided services
returns an array keyed by our button names. And then this is just some sort of
internal code that tells Symfony how to wire up each of these each of these services
inside the service container. So what we need is to just get the array keys of this
and return them. So let's just do return array keys. There we go. So what this so by
do by by by calling get provided services, we're no longer instantiating all the
services just to get the key, which greatly improves the performance, especially in
larger apps. So now we let's go back to our browser and refresh and refresh. And
everything is back working as expected. One, one note about this service collection
interface is if you look inside, if you jump in, it's in the Symfony con, I'm not
going to talk about this. Okay, and that's it, our app is fully now refactored and
performant at the same time is our app is now fully refactored to the command
pattern, we're able to list our buttons programmatically, and it's all done in the
most performant way possible. Next, we're going to look at the next, we're going to
add logging to our remote. Next, we're going to add logging to our next, we're going
to add logging to this, to our remote, and we're going to do it in a way that
separates the concerns so that's next.

