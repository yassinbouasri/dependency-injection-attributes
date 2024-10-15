# Enable Services in Specific Environments with When

Let's add a new "Diagnostics" button... but only to be available in the `dev`
environment. Tricky...

## Adding a Diagnostics Button

First, create the new button just like we did a couple chapters ago with "Mute".
In the `App\Remote\Button` namespace, create a new `DiagnosticsButton` class.
Have it implement `ButtonInterface`, and have the `press()` method
`dump('Pressed diagnostics button')`. Add the `#[AsTaggedItem]` attribute
with `diagnostics` as the index.

Finally, copy the `diagnostics.svg` file from the `tutorial/` directory into
`assets/icons/`.

Now, spin over to our app and refresh. There's our new button. Perfect! Give it
a press to be doubly sure it works.

## `#[When]`

We want to have this new button registered with the service container *only*
in the `dev` environment. The `#[When]` attribute is perfect for this.

Back in `DiagnosticsButton`, add the `#[When]` attribute with `dev` as the
argument. Now, this class will be completely ignored by the service container
unless we're in the `dev` environment.

In our app, refresh... and... it's still there...

That's expected as we're currently in the `dev` environment. To test that this

For the purposes of this example, let's change the `#[When]` attribute to `prod`
so we can prove that it's working.

Back to the app, refresh the page and... the button is gone! Awesome!

## `#[Exclude]`

Let's talk about the cousin of `#[When]`: `#[Exclude]`. This attribute tells
Symfony to *never* register the class as a service in the service container.
Simple as that.

In `config/services.yaml`, the `App/:` section tells Symfony to autowire all
the classes in the `src/` directory. Notice the `exclude` key with a list of
paths that Symfony will ignore. This is the traditional way to exclude classes
from being registered as services. This can get complex if you have a lot of
exclusions mixed in with your services. The `#[Exclude]` attribute can be an
easier way to handle these cases.

Test it out by adding the `#[Exclude]` attribute to our `MuteButton`. Jump
back to the app and refresh. The mute button is also gone!

You may not need this attribute often, but it's good to know it exists.

Next, let's talk about lazy services.
