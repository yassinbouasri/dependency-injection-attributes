# Enable Services in Specific Environments with When

Welcome back. So what, what I'd like to do now is add a new button, just like we did
before with the mute button, but it's going to be a diagnostics button and the
difference is, is we only want this button to be shown on the remote if we're in the
development environment. So when this is pushed to production, we do not want this
button to be displayed. So let's take a look at how we can do that. So the first
thing we need to do is create a new button under source remote button, and we've done
this before with the mute button. So we'll go new PHP class diagnostics button. And
just like all our buttons, we have to implement button interface and then control
enter to implement the methods, the press method. And just like the others, we are
just going to dump pressed diagnostics button. That's going to represent the logic
for this button. And just like all the others, we have to use the as tagged item. And
for the name, we will call it diagnostics. Perfect. So you remember the one other
thing we have to do when adding a new button is add the SVG icon to assets icons. So
for that, we will, if you look in the tutorial directory, you can copy the
diagnostics.svg into your icons folder. Okay. So let's jump back to our app and
refresh and perfect. There's our button. We can press it. All's good. All's good. So
what we want to do is, is only have this button in the service container. And what
that means is, is, so what we want to do now is only have this button shown when
we're in the development environment. So in all other environments, it will not be
auto-wireable. And most importantly for us is it won't be added to our button remote
via the auto-wire locator. So let's jump back to our app and we'll use our next
dependency injection attribute. And this one's called when. And then, oh, it will
import the class. And we'll use our next dependency injection attribute called when.
All right, here it is. Let's auto-import it. And you can see here, all it takes is
the environment that, and this is the environment that you want the class to be
registered in. So in our case, we want it to only be registered in dev. Okay. So
let's jump back, refresh the page. Okay. We are in the development environment, of
course, because this is how we're developing, because we have the profiler and
everything else. So what we, for the, so for the purposes of this example, let's just
change the environment, the when environment to prod. I know we said that we only
wanted it available in dev and that'll be true for when we push the class to prod,
but we don't want it to be available in dev. And that'll be true for what, you know,
when we push this app out to production. But for right now to test that this is
working, we'll, we'll change it to prod and then it should be hidden when we refresh
the page and sure enough, it is. Okay. So that's, that's the when, but there's
another sort of similar attribute that I want to talk about, and this one's called
exclude. So exclude is kind of like, you know, a total escape hatch. If for some
reason you have something in your app that's being registered or auto-wired and
you're not, and you really don't want it, or there's, it's kind of only useful if
you're running into problems where, where a class that you've, a service is being
tried to be injected somewhere and you don't want it to, you want to say, I want this
fully excluded from the service container. I never want it used by the Symfony
service container. Now there has already been a way for a long time to do this. And
this is in, if you look in config services.yaml, this is kind of the standard config
that comes when you install a new Symfony app. But if you look at the, under this app
namespace, which references all files, all classes under the source directory. So
anything in here, so this basically says anything in here can be auto-wired and
auto-configured, but you can see that there's this exclude key here, and we have a
list that Symfony provides us out of the box of things that it doesn't want to be
auto-wired or registered at all. So it doesn't want any of your doctrine entities
auto-wired and that makes sense, but it also doesn't want kernel.php, you know, to be
auto-wired. It doesn't want kernel.php registered in the container, and that's
because Symfony does a very special thing with kernel.php. It's still a service, but
it's a kind of a special service that Symfony has to wire up in a specific way. So
this is one way, and we could, you know, add a class here and then add it here. But
in keeping with not having to jump into YAML whenever we want to do something with
related to services, there's also an attribute to do this. So let's, for our example,
jump into our button and let's say we don't, we no longer want the mute button
registered. So we could delete it and that would be fine and that would totally work,
but maybe we want to use it for somewhere else for some reason. It's, it's, that this
isn't something that you reach for very often, but I just wanted to show it to you it
exists. So let's go into our mute button and we will add the, the, the the exclude
attribute and there's no arguments. Now, if we jump and that basically says, do not
register this in the service container, do not add it to any auto-wired locators
automatically. Just basically ignore this file when building the service container.
All right. So now if we refresh the page and now the mute button is gone also. So
that's being fully excluded. So it's not a very common need, but it's just something
to be aware of and that it does exist if you ever run into problems with Symfony
basically trying to, trying to auto-wire or auto-configure classes that you are
instantiating manually in your app and want, and don't want handled by the service
container in any way. All right. So the next, next we're going to talk about lazy
services, and this is a way that you can inject your services that in a way that they
will only be instantiated when and if they're needed. So let's take a look at that
next.
