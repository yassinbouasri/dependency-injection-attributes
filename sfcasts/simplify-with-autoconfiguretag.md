# Simplify with `#[AutoconfigureTag]` and `#[AsTaggedItem]`

Okay, welcome back. So in the last chapter, we refactored the big switch statement
that we had in our controller to this little try-catch. And basically the same logic
for our controller. The same logic. Our remote works the same way it did before, but
we've refactored it to the command pattern. So what we did is we used this autowire
locator to tell Symfony how to wire up this container. This is great. Totally valid.
But there's a better way. But there's a simpler way. What we can do is, because all
of our buttons implement this button interface, we can go in here and we can use our
second dependency injection attribute. And this one's called autowire tag. And this
one's called autoconfigure tag. And we'll import it. So what this does is when
Symfony is building your applications container, it's going to look through all the
services in here, and it's going to find anything that implements a button interface
now. And because of this attribute on the button interface, any class or any service
that implements this interface will get a tag. A tag is just basically a string that
tells Symfony to... It's just a string that's connected to a service. It's just a
string that's connected to a service. And by itself it doesn't do anything. But now
because they all have this tag, we can go back to our button remote and we can remove
this autowire locator. So we can remove this array here, this key value array, and we
can simply put button interface. When you're creating this autoconfigure tag, you can
give the tag a name. So we could call this button. But if you don't give it a name,
the tag name is the name of the interface, in this case button interface. So I'm just
going to use the default because the name of the tag is not terribly important. So
back in our button remote. So let's see if this still works. So we'll go back to our
app and let's press a button. Oh, error. Button power not found. Okay. So let's look
down. We can see it's calling this not found. So this not found exception interface
was thrown from our press method. And here we are. But if we look down, we can find
the previous. And this is going to get us some more information about what went
wrong. So service power not found. The container inside. So the container is that
old. So service power not found. So here it's basically telling us we don't have that
power. The power string is not. Service power not found inside. And this is telling
us our service locator, which is our container interface, doesn't have the button as
an ID for any services. So we can see. But this message does tell us what services
are in this container. And you can see here are the service IDs. These are the
service IDs. This is not what we want. But it is telling basically what happens. What
happens is when Symfony tAJAX a button, the ID of the service will actually be the
fully qualified class name of the service as an attribute on the tag. So what we
needed to do is use our third dependency injection attribute. And we'll call this.
And this one's called as tagged item. This allows us to customize the name of the tag
attribute. So the tag name itself will be button interface. But each tag can have a
key that represents the service that will be used in our button remote here. And what
we want to do is have it match the name of that slug name of our buttons. So in this
case, we'll add the first argument here. And we'll call it channel down. We'll go to
the next one and do the same thing. Channel up. Power. And volume up. So now what
this will be the key in our. This will be the ID of the service in our button remote.
So now if we go back to our app, this should be working. So let's refresh the page.
And press a button. Power pressed. Good. Channel up pressed. Channel down pressed. If
we look at our profile, we can see that it's working as expected. It's dumping the
change to channel down message that represents the logic for the channel down button.
If we swing over to our terminal, we can kind of see how this is working. If we swing
over to our terminal, we can see how this is working. To get better visibility on how
these tAJAX are working, we can jump over to our terminal and run bin console debug
container. And let's just search for button. And this is going to give a list of all
of our buttons. So these are our services. And let's just look at the power button.
So number two here. And we can see it has. I'm not going to show the terminal here.
So that's great. Now every time we add a button, all we have to do is create the
button in here, have it implement the interface, throw this as tagged item with a
unique button name, which will be the ID, and then we have to go into our template.
And here's where we list all the buttons in our front end. So we just would have to
add another list item with the button in it, with the new button in it. This is great
and totally valid, but I think we can do better. I think what would be nice is if we
didn't have to edit this file every time we added a new button. Since we have all
these button services in our button remote service container, we can just list. Since
we already have these, since we know what buttons we have here, let's add a method
here to list the buttons, the button names, and then in our template, we will just
loop over each of them and render the buttons. That way, when we add a new button, we
never have to touch this file. It just automatically adds it to the controller. It
automatically adds the button into the controller. Let's look at that next.

