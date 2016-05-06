# dk-web-authentication

Form based authentication flow using AuthenticationInterface. It provides a login/logout action and an Unauthorized error handler that handles AuthorizationError and redirect back to the login page. This way, you can send an AuthenticationError to this handler from anywhere in your code where you want to restrict access. The error handler will redirect to login with an additional error message.

### Installation
```bash
$ composer require n3vrax/dk-web-authentication
```

Until we register the package with packagist, you must add the following repositories to your composer.json
```php
  "repositories" : [
    {
      "type": "vcs",
      "url": "https://github.com/n3vrax/dk-authentication"
    },
    {
      "type": "vcs",
      "url": "https://github.com/n3vrax/dk-error"
    },
    {
      "type": "vcs",
      "url": "https://github.com/n3vrax/dk-web-authentication"
    }
  ],
```

Even tough it is possible to initialize and configure this module manually, by creating a `WebAuthOptions` class and sending the dependencies to the actions, you'll usually configure it through the container-interop.
We'll cover the DI container initialization and configuration only

### Usage

#### Registering route dependencies
In your `routes.global.php` register as dependencies the `LoginAction` and `LogoutAction` by using their classname as key and the provided factories as value(LoginActionFactory and LogoutActionFactory of this package)
```php
return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
            //...
        ],
        'factories' => [
            \N3vrax\DkWebAuthentication\LoginAction::class =>
                \N3vrax\DkWebAuthentication\Factory\LoginActionFactory::class,

            \N3vrax\DkWebAuthentication\LogoutAction::class =>
                \N3vrax\DkWebAuthentication\Factory\LogoutActionFactory::class,
        ]
    ],
```

#### Adding the routes

Next, register 2 routes to these Action middlewares, in the same file, like this, make sure the login action has POST method allowed
```php
   'routes' => [
        //...
        [
            'name' => 'login',
            'path' => '/login',
            'middleware' => \N3vrax\DkWebAuthentication\LoginAction::class,
            'allowed_methods' => ['GET', 'POST']
        ],
        [
            'name' => 'logout',
            'path' => '/logout',
            'middleware' => \N3vrax\DkWebAuthentication\LogoutAction::class,
            'allowed_methods' => ['GET']
        ]
        //...
```

#### Registering the UnauthorizedHandler

In your `middleware-pipeline.global.php` register an error handler like this, both in dependencies and middleware-pipeline
```php
        'dependencies' => [
            'factories' => [
                //....
                \N3vrax\DkWebAuthentication\UnauthorizedHandler::class =>
                    \N3vrax\DkWebAuthentication\Factory\UnauthorizedHandlerFactory::class,
            ],
        ],

    'middleware_pipeline' => [
        //....
       'error' => [
            'middleware' => [
                // Add error middleware here.
                \N3vrax\DkWebAuthentication\UnauthorizedHandler::class,
            ],
            'error'    => true,
            'priority' => -10000,
        ],
```

#### Configuring the module

Up until now, all the configuration was meant to register dependencies with zend-expressive. Next, we need to inform the package about the routes and templates we want to use. In order to do this, add the following section in your `authentication.global.php` file, along the adapter and storage configuration

```php
'authentication' => [
    'adapter' => [
        //....
    ],
    'storage' => [
        //...
    ]
    //this is the part
    'web' => [
        //login route name as specified in router.global.php
        'login_route' => 'login',
        //logout route name
        'logout_route' => 'logout',
        //the template name to use for the login page
        'login_template_name' => 'app::login',
        //the following 2 router specify where to send user after logging in and logging out
        //consider we have a route account with my account page
        'after_login_route' => 'account',
        'after_logout_route' => 'login',
        //the following is optional, it is a callback to be called before authentication happens
        //if not given, no action is performed before auth
        //this is the place where you can take credentials from POST data and validate and prepare it for the auth adapter
        'pre_auth_callback' => 'Some callable service name',
        //enable wanted url redirect - after login go to the requested page feature, optional, enabled by default
        'allow_redirects' => true
    ]
]
```

### Pre-authentication callback

This is the last place where you can work on the authentication credentials before passing them to the authentication service. Because authentication service usually use adapters to perform authentication, each adapter has it own requirements regarding where and how credentials must be store in the psr7 messages. Also, you might want to check credentials beforehand and perform validation.

If you are writting a pre-auth callback, the callable signature is ` public function __invoke(ServerRequestInterface $request, ResponseInterface $response)`.

You have 3 posibilities regarding what can you return from this callable
* return a `\N3vrax\DkWebAuthentication\PreAuthCallbackResult` if you want to modify the request/response objects. The LoginAction will extract these from the result and pass them to authentication. This way, you can add attributes or headers to the request with credentials, based on the authentication service needs. One example could be a POST request->extract identity and credential->set them as an attribute required by the authentication

* return a ResponseInterface concrete implementation. LoginAction will returned this response directly.

* return `null|false` will make the LoginAction ingore the result, the same as if pre-auth callback was not defined


### Login template

Regardless of what engine you use, the login template you define will possibly received the following data
* `messages` - variable containing authentication error messages as array. You should display them on top the the login form in red.
* `identity` - variable containing the identity the user has typed. This is usefull if credentials were invalid and you want to keep the previous erroneous username/email/etc. printed in the form

Here's a complete login.phtml example using Plates as renderer
```html
<?php $this->layout('layout::default', ['title' => 'Login']); ?>
<?php
$messages = isset($messages) ? $messages : [];
if(!is_array($messages))
    $messages = array($messages);

$identity = isset($identity) ? $this->e($identity) : '';
?>

<?php if(!empty($messages)): ?>
    <div class="alert alert-danger" role="alert">
        <ul>
            <?php foreach ($messages as $message):?>
                <li><?=$this->e($message)?></li>
            <?php endforeach;?>
        </ul>
    </div>
<?php endif;?>

<form method="post" style="width: 500px;">
    <div class="form-group">
        <label for="identity">Identity: </label>
        <input type="text" name="identity" class="form-control" id="identity" placeholder="Username or email..."
            value="<?=$identity?>">
    </div>
    <div class="form-group">
        <label for="credential">Password: </label>
        <input type="password" name="credential" class="form-control" id="credential" placeholder="Password...">
    </div>
    <input type="submit" class="btn btn-default" value="Sign In">
</form>
```


