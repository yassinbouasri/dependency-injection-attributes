# Alias an Interface with AsAlias

Now that we've successfully refactored our buttons so we can easily add *new* buttons that automatically appear on our remote, let's work on adding some new features. How about adding a little log message before and after pressing a button?

We *could* inject the Symfony logger here, but that would *technically* violate the Single Responsibility Principle of this class. *Basically*, this class would be doing too much. We're already clicking the button and running the button logic. If we also add logging, we're doing something unrelated to pressing a button.

*Instead*, we're going to decorate this button remote with a *new* logger remote who's only responsible for doing the logging and calling the button remote logic inside of it, while keeping it separate. So the button press logic will be *here* and the logging remote will only care about logging.

When using decoration, you could *technically* create a logger remote class that has the same API here, and that *would* work, but it's best practice to add an *interface* and use decoration with interfaces. *So*, the first thing we're going to do is create a remote interface. In the `Remote/` folder, create a new PHP class called `RemoteInterface`. We need to add two methods from `ButtonRemote.php` here, so head over, copy the this method here, and... *paste*. So we have `press()`... and then, down here, we also need our list of buttons. Copy the whole block here... paste... and then add a semicolon. Nice! Interface *done*!

Now we need to *implement* `RemoteInterface` in `ButtonRemote.php`. And... perfect! PHPStorm seems to be happy, so we're sucessfully implementing our interface. The next step is to inject the *interface* into our controller. Symfony allows autowiring concrete classes as well as interfaces but, whenever possible, you should stick to using interfaces, since it just makes your code easier to extend later on. Over in our controller... right here, we're going to replace `ButtonRemote` with `RemoteInterface`... and change this typehint to `$remoteInterface`.

Okay, let's jump back to our app and make sure everything's still working. Refresh, and... looks great! Now we need to add a new implementation of `RemoteInterface` for the logger, so, back in our code, we'll add a new PHP class called `LoggerRemote`. *Implement* our `RemoteInterface`... and then use "command" + "enter" here to implement the methods.

All right, our service needs the logger of course, but since we're using service decoration, we also need to inject another instance of `RemoteInterface` - the one we're decorating. So add a constructor here... and then we can clean this up a little. Next, we're going to inject `LoggerInterface` - the one from "Psr\Log" - and `RemoteInterface`. Just like with our controller, it's best to inject the *interface* instead of the concrete class whenever possible.

Now we want to defer `buttons()` to our decorated service. We're not going to do any logging here, so let's just call `$this->remote`... and... hm... I'm actually going to rename `$remote` to `$inner` so it's *super* clear that this is the inner service we're decorating. So, say `$this->inner->buttons()` and `return` it. Done! We'll do the same thing up here. Call `$this->inner->press()` and pass the `$name`. Now this decorator is deferring all of the calls to the `$inner` service that it's decorating, but we *still* need to add our logging. So *before* we press the button, we'll add this logger with `$this->logger->info('Pressing button {name}.')` with a context array where we'll pass `'name' => $name`.

This is a little syntax that monolog uses, which is the default Symfony logger, and it's basically a templating string to inject your context variables into the string. *So*, copy this and paste it *after* we execute the button press... and instead of `Pressing`, we'll call `pressed`.

Okay! Let's go back to our app and see if everything's still working. Refresh and... uh-oh, an *error*. It looks like our controller requires a `$remote` argument that couldn't be resolved.

`Cannot autowire argument $remote of
"App\Controller\RemoteController::index()": it
references interface "App\Remote\RemoteInterface"
but no such service exists.`

This tells us that our interface has *two* implementations. There's a `ButtonRemote` implementation and a `LoggerRemote` implementation. Basically, Symfony doesn't know which one to use. When we were only using `ButtonRemote`, since there was only one service that implemented `RemoteInterface`, Symfony knew to use *that* one, but now we've complicated things a bit. How do we fix it? This error message gives us a little hint. 

`You should maybe alias this interface to one of
these existing services: "App\Remote\ButtonRemote",
"App\Remote\LoggerRemote"`.

It looks like we could use an *alias*. In Symfony, an alias allows us to determine which service should be used when someone asks for the `RemoteInterface`. To do that, we need to deploy a new dependency injection attribute - `asAlias`. So let's find the service that we want to be aliased with `RemoteInterface` and make sure that it does, in fact, implement `RemoteInterface`.

At the top here, we can add the attribute `asAlias`, which tells Symfony that any time we ask for an interface this service implements (in our case, just `RemoteInterface`), then it needs to inject *that* one. If we go back to our app and refresh... perfect! It's working again!

Okay, that's done, but we're still not logging. We a

Next: Let's look at how we can decorate `ButtonRemote` with `LoggerRemote` and make them work *together*.
