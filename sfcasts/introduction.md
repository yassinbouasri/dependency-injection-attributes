# Introduction

Coming soon...

Hey friends, welcome to a new course that's all about Dependency Injection
Attributes. What are these? Well, let's have a little history lesson. A long time
ago, in a Symfony version far, far away, services, these are objects that do work,
had to be configured in separate YAML or XML files. Wiring up a service is the
hipster term for this. In these files, you'd create a service ID, reference your
service class, and reference any service IDs, parameters, or scalar values required.
While this worked great, it was a bit cumbersome. Anytime you added, deleted, or
modified a service's arguments, you'd need to jump into another configuration file
and update this also. There had to be a better way. And there is. A subsequent
Symfony version, still long ago, added something called auto-wiring. This enabled you
to just create a PHP service object. This enabled you to just create PHP service
objects, and any other services required would be automatically injected. You just
needed to type in the service class or interface. While this was a huge step forward,
for even the slightest more advanced scenarios, like a scalar argument from a
parameter, you'd need to still configure this in YAML or XML. It reduced the amount
of time spent in configuration files, but did not eliminate it. Then in PHP 8, then
PHP 8 came along. This added native attributes, metadata that can be added to class
properties, methods, method arguments, and more. This was the perfect feature to
allow us to do all the configuration we require right in our service classes. With
Symfony 7.1, the version we'll use in this course, there's a plethora of DI
attributes to use. We're going to look at each of them. You'll find only rare, super
advanced scenarios where you need to actually edit a separate configuration file. For
the most part, your services.yaml file, which is included with a standard new Symfony
app, will be all you ever need, and will hardly ever change. We'll showcase all these
attributes in a fun little app. Here's the scenario. You have a smart TV, and you've
hopelessly lost the remote. You cannot find it. Who knows what your kids did with it?
You've placed an order for one online, but it's hopelessly backordered. You've looked
online and found a replacement, but it's hopelessly backordered. What are we going to
do? Well, you've discovered that an API exists for your smart TV. As web developers,
we can use that to create our own remote, and have it accessible via a tablet within
our home network. So to follow along, download the course code for this video. Open
the code in your IDE of choice, and follow the README. I've already done this. So I'm
going to spin over to my terminal and run Symfony serve-d to run the server in the
background. I'm going to command click the URL here to open up our app in the
browser. As you can see, we have our remote. Pressing the buttons allows us to do
various things like change the channel, power on and power off, and increase and
decrease the volume. If we jump back to the code, you can see it's a fairly
straightforward Symfony app, almost right out of the box. I've just added a single
controller and template. This remote controller, which represents our remote, and
here all we do is check to see if a method is a post. If it's a post, that means a
button was clicked. We check which button it was, and in lieu of actually writing the
logic to activate the specific button through our API, we're just going to dump so
that, for our example, we know which button was pressed. If a button is not found, we
create a 404, and then we're adding a flash message after each button is pressed, and
then redirecting back to this exact same route. Very standard Symfony stuff, and then
when it's not a post request, so a button wasn't pressed or we've been redirected, we
just render this index page, and if we jump into this using command click, very
straightforward template. We're just rendering the flash messages and then rendering
the controller here, each button. In the next chapter, we're going to look at our
first dependency injection attribute, the auto wire locator, to refactor this switch
statement into the command pattern.
