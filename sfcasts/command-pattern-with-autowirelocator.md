# Command Pattern with `#[AutowireLocator]`

Okay, let's get started. So looking at our app, this is our remote little UI here, is
basically just a form and each button here on the form submits the form. It's a
submit button with a name of the button that our controller uses to determine which
button and which button logic to execute. So you can see when we, you know, click the
power button here, we see this flash message and this is added by the controller
also, tells us, you know, what happened. So the power button was pressed and then
here channel up was pressed, channel down. This form just redirects to the same page
or just posts to the same page and then handles the logic of the button and then
redirects us back with this flash message. So we can look in the profiler to see what
that post request looked like by clicking here and you can see the last button we
pressed was the channel down. So we're just seeing this dump from the remote
controller that is representing the logic of the channel down button. So let's go
back here and then let's go to our controller. So we can go into src/Controller,
remote controller and here we go. So we could, we're checking if the request is a
post and then we have a switch statement here where we're grabbing the button name
from the request. So that's what each of those buttons submits with a name and then
we're doing this for each button, we're wrapping in a case for, you know, power,
channel up and then these are the dumps that are going to represent the logic for
each button. And then if we can't find the button we just throw a 404 here with a
little message and then here's where we add the flash and we're just doing a little
bit of string manipulation here to take the button name which is kind of like a slug,
lowercase with dashes and then we're replacing the dashes with a space and then we're
title casing it so it looks a little nicer on the flash message and then we're just
redirecting right back to the same route. At the bottom here if we're not in a post
request we're just rendering our index.html.twig which is our remote UI. So when you
have a big case switch case statement like this it's a really good opportunity to
refactor to something else that allows you, because you can imagine these as we add
buttons, as we add more logic to each button, this could get pretty large. So a great
refactor to this is to use the command pattern. Do a little blurb about the command
pattern and a way to search on Symfony casts to find our design pattern course with
the command pattern for more information. So the first thing we're going to do is
create our commands and these will be the buttons and they will house all this logic.
So for that we're going to create a new directory in our source just to organize our
code a bit. So we'll call this remote and then inside we're going to create another
subfolder called button and then we're going to create a new php class. We're going
to create an interface that will represent that each button will need to implement so
that the command so that our command handler can pull these in a predictable way by
using because it knows that each button will have this button interface. So we'll
call it button interface and you can see because we put interface in phpStorm is
automatically going to create an interface for this. So go okay and great here's our
interface. So we're going to add one method one public method press and it's not
going to have any arguments and it's just going to it's not going to do any it's not
going to return anything so we'll just have it return void. All right so for the
actual button implementations I have these already created in the tutorial directory
so if you can just you'll just copy these all all the button.php files into this
button directory and there we go and you can see each button here has the
implementation of the press method and we just kind of are moving that dump that
represents the logic for each button into the press for each button. So this is the
channel down button so change channel down is our dump message and this is the same
message that's that's in our controller right now. So with the command pattern we
have our commands which are our buttons and now we need a command handler something
that's going to take the button name and execute the command based on that. So for
that we're going to create a remote object that's going to represent that will be our
handler a command handler. So under remote we'll create a new class and we'll just
call it button remote. I'm going to throw a final on here because that's just the
pattern I like to use I never I always create my classes as final and remove final if
it ever does need to be extended. That's just a personal preference of mine feel free
to leave the final off if if that's not part of your workflow. So in here we're going
to create a public method called press again and it's going to take a and it's going
to take a single argument string name this will be the name of the button and this
will be the name of the button.
And again, it will be void, it's not going to return anything. And then we're going
to create a constructor for this object. And it's going to take a container
interface. So we'll... So grab the one from PSR container. And this will be... So
this container is going to contain all the buttons that we need to press. So if you
take a quick look at this container interface, it's pretty simple. It's kind of like
a key value object store. So it's going to store our button objects. And this git
will get the command object, or in our case, the buttons, based on the ID, which in
our case is the name of the button. So we... And then back in our button remote,
under the press method, we're going to say this container git name. So now we'll have
an instance of button interface, so we'll just call press. Now, this in itself won't
do anything. It'll actually give us an error if we try to call this press method,
because Symfony right now does not know how to wire up this container. It doesn't
know that we want to add our buttons into here. So to tell Symfony about this, we're
going to use our first dependency injection attribute, which is the autowire locator.
If you're using an older version of Symfony, this existed, but it was called tagged
locator, and that attribute was deprecated in favor of this one in Symfony 7.1. So
it's the same thing. They changed the name to just be more consistent to know what
you're doing. You're autowiring something here. So inside this, our first argument on
the attribute, we're going to just add our keys for each of the button names, so
power, and then we're going to say power button class. So what this is going to tell
Symfony is when you're building this container, so when you come across this, when
you're building the full application, it's going to say, hey, when you hit this, for
this container, we're going to add what's inside this array as the key values, and
this is like the class name, but Symfony is going to convert this into the server, or
into the instance of each of the buttons. So let's go ahead and add all of our other
buttons here. Channel up. Channel down. Volume up. Volume down. Okay. So we have all
of our buttons wired up in this container, and this is our command handler, this
remote. So now we just need to replace that big switch, the big switch case statement
in our controller to use this remote instead. So we'll go back to our controller, and
up here we will, let's just give us a little bit more room here. And after the
request, let's inject button remote. Because this controller is auto-wired, Symfony
will automatically know to inject this when it comes across it. And then down here,
we will, first we'll copy this to get the button name. And then what we're going to
do, is take the remote object that we injected, and we're going to call the press
with the button name. Now we can basically remove this full switch statement, but we
haven't dealt with what happens if a button is not found. Create the not found, the
404, if the button doesn't exist. So we're going to copy this line here, and then now
we can delete this whole switch statement. And then we're going to wrap this press in
a try, in a try catch. And what we're going to catch is a not found exception
interface. Not found exception interface. So when, and then grab the E, and then
we'll paste our code here. So when in our button remote, in the press, when get
receives a name that it doesn't have a service or a command for, it's going to throw
this not found exception interface. It's going to throw an instance of this not found
exception interface. So the one thing we'll do here, just to help us with debugging,
is we will add the previous exception, which is the not found exception, to this 404.
Okay. So yeah, our controller is looking quite a bit smaller now, so let's go back to
our app and see how that worked. So let's press the power button. Power pressed.
Channel up pressed. Okay, it seems to be working. And then if we look back into the
profiler and find the profile for the post request, we can see we still have this
dump message, and as you can see, it's coming from the channel down button. So it is
executing the proper command based on the button name. So this is great. If we go
back to our button remote, there's one thing that I think we can improve here, and
every time we add a new button to our app, we're going to have to come in here and
tell this auto wire locator about the new button. So that's kind of, it's totally
valid, but it is a bit of an annoying thing. So in the next chapter, let's look at
another refactor to remove that requirement. That's next.
