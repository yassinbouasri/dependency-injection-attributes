# Enable Services in Specific Environments with When

Wouldn't it be cool if we had a special *secret* button on our remote we could use to make sure it's working correctly? *Of course* it would! Let's add a sneaky new "Diagnostics" button that will *only* be available in the `dev` environment.

## Adding a Diagnostics Button

The first thing we need to do is create a new button the same way we created our "Mute" button a few chapters ago. In `App\Remote\Button`, create a new class called `DiagnosticsButton`. Make that implement `ButtonInterface`... and hold "control" + "enter" to add the `press()` method. Inside, we'll `dump('Pressed diagnostics button.')`... and, just like before, we'll add the `#[AsTaggedItem]` attribute with `diagnostics` as the index. Finally, copy the `diagnostics.svg` file from the `tutorial/` directory into `assets/icons/`. Cool!

Okay, if we spin over to our app and refresh... *there's* our new button! And if we press it... it looks like it's working!

## `#[When]`

Now we need to register our new button with the service container, but *only* for the `dev` environment. The `#[When]` attribute is *perfect* for this. Back in `DiagnosticsButton.php`, add the `#[When]` attribute with `dev` as the argument. This means that this class will be *completely* ignored by the service container *unless* we're in the `dev` environment. If we head over and refresh again... it's *still* there. That's *expected* since we're currently in the `dev` environment, so let's make an adjustment. Change the `#[When]` attribute argument from `dev` to `prod` so we can see what our remote looks like from the production side of things. Refresh the page again and... boom! The button is *gone*! *Awesome*!

## `#[Exclude]`

Now that this is working, let's talk about the cousin of `#[When]`: `#[Exclude]`. This attribute is like a big warning sign that tells Symfony to *never* register a specific class as a service in the service container. Right now, in `config/services.yaml`, this `App/:` section tells Symfony to autowire all of the classes in the `src/` directory, and *inside* `App/:`, we have this `exclude` key, which contains a list of paths Symfony should *ignore*. That's the *traditional* way to exclude classes from being registered as services, but it can get *complicated* if you have a lot of exclusions mixed in with your services. That's where the `#[Exclude]` attribute comes in! In `MuteButton.php`, up here, add `#[Exclude]`... then head back to our app and refresh. The "Mute" button is *gone*! It *worked*. While you may not need to use this attribute often, it's good to know it exists.

Next: Let's talk about *lazy* services.
