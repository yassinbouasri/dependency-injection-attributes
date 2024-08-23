# Introduction

Hey friends! Welcome to a brand new course that's all about *Dependency Injection Attributes*. What *are* Dependency Injection Attributes? Well, here's a little history lesson:

A long time ago, in a Symfony version far, far away, *services* (which are objects that do work) had to be configured in separate YAML or XML files. The cool kids called it "wiring up a service". In these files, we'd create a service ID, reference our service class, and reference any service IDs, parameters, or scalar values required. While this worked great, it *was* a bit cumbersome. Anytime we neeed to add, delete, or modify a service's arguments, we'd have to jump into *another* configuration file and update *that* as well. *Surely* we could find a better way to do that, right? You bet!

In a *subsequent* version of Symfony, *still* long ago, they added something called "autowiring". This allowed us to just create a PHP service *object*, and *any* other services required would be injected *automatically*. We just
needed to type in the service class or interface. While this was a *huge* step forward, we still had to configure this in YAML or XML, even in only slightly more advanced scenarios (like scalar arguments from a parameter). It definitely *reduced* the amount of time spent in configuration files, but it didn't *eliminate* it.

*Then* PHP 8 came along and added native attributes, metadata that can be added to class properties, methods, method arguments, and *more*. This was the perfect feature to enable us to do *all* of the configuration right in our service classes. With Symfony 7.1, the version we'll use in this course, there's a *plethora* of DI attributes to use, and we'll take a look at each of them. We'll see that editing a separate configuration file is only necessary in rare, super advanced scenarios. For the most part, the `services.yaml` file, which is included in every new Symfony app, is all we'll ever need, and it will hardly ever change. We'll showcase *all* of these attributes in a fun little app so you can see how they work.

So here's the scenario: We have a smart TV, but we've lost the remote. We searched *everywhere*, but it's nowhere to be found. I bet those pesky remote gnomes took it to sell on "Gnomeslist"...

We found a replacement remote online, but it's backordered and it'll take *forever* to get here. What do we do? Well, while we were looking for a solution on "RemoteOverflow", a user (who is definitely *not* a gnome) told us about an API that exists for our smart TV. As web developers, we can use that API to create our *own* remote we can access and use on any tablet connected to our home network. Thank goodness the gnomes didn't steal our tablet, right? Right..?

To code along with me, download the course code for this video, open it in your IDE of choice, and follow the setup steps in the `README.md` file. I've already done that. Now we can spin over to our terminal and run:

```terminal
symfony serve -d
```

to run the server in the background. Hold "command" and click on the URL here to open up our app in the browser, and... there's our remote! It does all of the usual remote things like changing the channel, powering the TV on and off, and increasing or decreasing the volume. If we go back to our code, we can see that it's a pretty straightforward Symfony app right out of the box, aside from a single controller and template I added.

Inside `RemoteController.php`, which represents our remote, all we do is check to see if a method is a `POST`. If it *is* a `POST`, that means a button was clicked. We check to see *which* button was clicked and, at the moment, instead of actually writing the logic to activate that specific button through our API, we're just using `dump()` so we can just see which button was pressed and know that this is hooked up correctly. If a button is *not* found, this will create a 404. Each button click is followed by a flash message, and then we're redirected back to this exact same route - pretty standard Symfony code. If a button *wasn't* pressed or we've been redirected, this just renders an index page. And if we jump into *that* by holding "command" and clicking on it, we can see that this is a pretty typical template. We're just rendering the flash messages and each button of the controller here.

Up next: We're going to look at our *first* dependency injection attribute - the *autowire locator* - to refactor this `switch()` statement into the command pattern.
