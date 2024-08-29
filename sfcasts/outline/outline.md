# DI Attributes

## Introduction

- smart tv webapp
- lost smart tv remote
- tv has an api
- api is ugly, different, complex logic is required for each button
- https://symfony.com/doc/current/reference/attributes.html#dependency-injection
    - `#[Autowire]` already covered in SF7 EP2

## Command Pattern with AutowireLocator

- Previously called `#[TaggedLocator]`
- Create `ButtonInterface`
- Copy buttons from tutorial directory
- Use array with AutowireLocator to inject directly in constructor
- Not ideal, each time we add a button, we need to update the attribute array

## Simplify with AutoconfigureTag and AsTaggedItem

- add to ButtonInterface
- replace AutowireLocator array with interface name
- broken... container keyed by class name
- add AsTaggedItem to each button with service id
- working again!

## List Buttons with AutowireIterator

- Let's remove the need to update our template each time we add a button
- Previously called `#[TaggedIterator]`
- Need to set the `AutowireIterator::$indexAttribute` to `key`
- Buttons are now added/removed dynamically
- Use `AsTaggedItem::$priority` to adjust order
- Pressing buttons is now broken!

## Container and Iterator with ServiceCollectionInterface

- We could add two ButtonRemote constructor but there's a better way
- Switch back to `AutowireLocator` attribute
- Can remove the `indexAttribute: 'key'`
- `ServiceCollectionInterface` is both a container AND an iterator
- Utilize `array_keys(ServiceCollectionInterface::getProvidedServices())` to lazily list the buttons
- Added and utilized in Symfony 7.1

## Alias an Interface with AsAlias

- create `RemoteInterface` and have `ButtonRemote` implement
- use interface in controller
- ensure works as normal
- Add `LoggerRemote` and implement interface
- Broken - Symfony doesn't know what concrete object that implements interface to use
- Add AsAlias to ButtonRemote
- Logging not working yet...

## Decorate a Service with AsDecorator

- use AsDecorator on LoggerRemote
- Logging works!
- Mention `AutowireDecorated`

## Enforce Named Autowiring with Target

- add custom button logging channel
- show debug:autowiring output with buttonLogger
- use named autowiring to change argument to $buttonLogger
- show logging in correct channel
- rename custom channel to "remote"
- show how it falls back to app channel - not super important for logging but you can imagine this being a big problem with other services
- add Target to logger arg
- now works as expected
- rename channel again - to "buttons"
- hard error - but this is what we want!
- edit the Target argument
- works again!

## Enable Services in Specific Environments with When

- add a diagnostics button
- we only want available in development
- add When
- to show that it works, temporarily change When env to `prod` and show that it disappears
- Mention `Exclude`

## Lazy Services

- describe lazy services...
- add Lazy to `OnButton`
- show error: cannot be final
- remove final (tip: add to docblock to help with static analysis and your IDE)
- debug container for on button - it's lazy
- show it's working still
- re-add final, use the interface in Lazy
- show it's still working
- add to ButtonInterface to make all instances lazy - resolve problems...

## More Laziness Attributes

- Power button: if tv is off, requires a "heavy to instantiate" Alexa service, we don't want this loaded when the tv is on
- AutowireServiceClosure
- AutowireCallable
- AutowireMethodOf
