# List buttons with `#[AutowireIterator]`

Okay, now that we've successfully refactored our app to use our controller to use the
command pattern for executing the buttons, let's look at how we can change this
interface from being hard-coded for each button in our template to be dynamic. So as
we add new buttons, we don't have to jump into our template and edit it. So the first
thing we're going to do is go into our button remote class and we are going to first
change... So we need to list all the buttons and their IDs, their service IDs. So I
think what we're going to do is create a public method here called buttons and this
is going to return an array. And the type, we'll just do add some doc blocks here to
just say it's going to be an array of strings. These will be the button IDs. So the
autowire locator is for a container and our container cannot iterate over the buttons
that are in it, over the concrete buttons that are in it. So what we want to do is
change this to autowire iterator, a new attribute called autowire iterator. And when
you do this, it's no longer going to be a container interface. What this tells it is
to inject an iterator. So we can just type hint at this to iterable and then let's
just rename this container now to just buttons. And then down here, we're going to
loop over the buttons. So we'll create an array and then we'll do a for each. So
we'll create an array and then we'll do a for each. This buttons as we're going to,
it's going to be keyed by the button ID. So we'll call it name and then button. That
will be the actual service. Now we're going to completely ignore the service and
we're just going to grab the name and add it to this buttons array. And then down
here, we're just going to return buttons. So let's hop back to our controller and
we're already injecting the button remote. So down here where we rendered the index,
where we rendered the template, we're going to just inject the buttons. Remote
buttons. Now to see what this buttons is going to return, let's just DD it here. And
jump back to our app and refresh the page and see what we get. Okay. So we're not
seeing what we want to see here. We're seeing just a list. We want this to be the
button names. So how we fix this is back in our button remote. If we go back up to
this auto wire iterator. So for the auto wire locator, it automatically uses our tag
attribute that we added here as the key or as the ID for the locator. But for the
auto wire iterator, when it injects this iterable, it keys it just by, it's just
keyed by the integers like the order that it comes in. So what we need to do is tell
it to use our attribute as the index, our key attribute as the index. So for this,
we'll add a second one called index attribute. And we're going to call it, we're
going to ask it to do, to index it by key. The key is the default. The key will be
what is used in the, this should be the key for each1. So if we go back to our
controller, we still have that DD. So let's go back to our app.
And refresh. And there we go. Now we have the actual button names. These are the IDs
within the container. These are now the IDs of the button. And we can loop over them
in our template and render each button programmatically. So let's grab our
index.html.twig. And right here we are creating an unordered list. So that's where
each button is. And we will, instead of hard coding them, we are going to loop over
the buttons that we're passing to our template. First thing what we have to do is
let's remove this DD. So we're going to grab these buttons in our template. So let's
add some space here. And then we're going to do for button in buttons. And if you
notice in our frontend, we have the first button is kind of bigger and red for the
power button. So we're just going to copy this line here. So what we're going to do
is do an if. And then this loop special twig variable first. So this will be true if
it's the first iteration of this foreach. And then we'll do... We'll add an else. So
when it's the first iteration, we want to render the button a little bit differently.
So it looks a little bit different than the other ones. And then for the rest, we're
just going to render them as normal buttons. So let's copy this. Because that's the
first button. And paste. We just need to do a little bit of... We just need to edit
this a little bit. So the name is button. And then the value, instead of hardcoding
power, we're going to put button here. We're going to render the button from our
loop. And this is the name of the button that should match our slug, which is the
service ID in our container. And then same here where we're using this icon. Instead
of hardcoding power here, we will put button here also. And this will render the
proper SVG file for this icon. It'll render the proper SVG for this button. So now
we'll go down here. And we are going to cut this line. So this is a standard norm.
Now we'll go down here and grab the second button. And this is just the standard
button template. And this will just be the standard button look. And then we'll go up
here. And in the else, we'll paste. And same thing as above. We're just going to
render the button variable as the value. And same thing as the name of this icon
button. So there we go. And now we can delete all the rest of these, all the rest of
the hardcoded buttons. Okay. So that simplifies this template quite a bit. Let's spin
back over to our app and refresh. And okay. It's rendering the buttons. But they're
not in the right order. We want this up top. We want, yeah, things are just not
ordered correctly. So what we need to do is enforce the order at which the... I wish
these are injected into the iterator. So how we can do that is if we open up our
buttons, in this adds tag item, there's another tag attribute called priority.
Priority is a way that you can order things and how they're going to be injected. For
our... When we had the auto wire locator, this wasn't important because we're not
iterating. But as soon as you start iterating over things, you might want to control
the order. So the best way to do this is... So the way to do this is we'll add a
priority. And let's call this one 50 because a higher priority means it'll be moved
up the... It'll be moved up the chain. So we're going to do a priority 50 for the
power button because we want that to be first. And then we will go to the channel up
button and we'll add a priority of 40. Channel down. Priority of 30. Volume up.
Priority of 20. Volume down. Priority of 10. If you don't add a priority, it is
considered priority zero. And then you don't have really any control over what order
they're going to be in your iterable list. So now if we hop back to our app and
refresh. All right. We're back in business here. So now we have everything... Now we
have all the buttons that are being added programmatically to our template. But you
might have seen that we have a little bit of a problem here. Let's check this out.
Let's press the button and check this out. Attempted to call undefined method git of
class rewindable generator. First of all, rewindable generator is the iterator that
Symfony injects. It's just a special wrapper around a traversable uniterable element.
So the problem here is when we refactored to use auto wire iterator, we no longer
have that container. So this is the failure here. It's calling git on something that
doesn't have a git method because it's no longer a container. In the next chapter, we
will fix this and get it working again and get our app working again.

