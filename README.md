# Navindex:Auth

***

Flexible Auth package for CodeIgniter 4. It is based on [**Myth:Auth**](https://github.com/lonnieezell/myth-auth) and provides additional customisation flexibility.

**NOTE: *This package is not functional yet. Watch this space to check the progress*.**

## 1. Requirements

- PHP 7.2+
- CodeIgniter 4

## 2. Features

This is meant to be a one-stop shop for 99% of your web-based authentication needs with CI4. It includes the following primary features:

- Password-based authentication with remember-me functionality for web apps
- Flat RBAC per NIST standards, described [here](https://csrc.nist.gov/Projects/Role-Based-Access-Control) and [here](https://pdfs.semanticscholar.org/aeb1/e9676e2d7694f268377fc22bdb510a13fab7.pdf).
- All views necessary for login, registration and forgotten password flows.
- Publish files to the main application via a CLI command for easy customization
- Debug Toolbar integration
- Email-based account verification

## 3. Installation

Installation is best done via Composer. Assuming Composer is installed globally, you may use
the following command:

```bash
    > composer require navindex/auth
```

This will add the latest stable release of **Navindex:Auth** as a module to your project. Note that you may need to adjust your project's [minimum stability](http://webtips.krajee.com/setting-composer-minimum-stability-application/) in order to use **Navindex:Auth** while it is in beta.

### 3.1. Manual Installation

Should you choose not to use Composer to install, you can clone or download this repo and then enable it by editing **app/Config/Autoload.php** and adding the **Navindex:Auth** namespace to the **$psr4** array. For example, if you copied it into **app/ThirdParty**:

```php
    $psr4 = [
        'Config'        => APPPATH . 'Config',
        APP_NAMESPACE   => APPPATH,
        'App'           => APPPATH,
        'Navindex\Auth' => APPPATH .'ThirdParty/auth/src',
    ];
```

## 4. Configuration

Once installed you need to configure the framework to use the **Navindex:Auth** library. In your application, perform the following setup:

1. Edit **app/Config/Email.php** and verify that a **fromName** and **fromEmail** are set as that is used when sending emails for password reset, etc.

1. Edit **app/Config/Validation.php** and add the following value to the **ruleSets** array:

```php
    public $ruleSets = [
        \CodeIgniter\Validation\Rules::class,
        \CodeIgniter\Validation\FormatRules::class,
        \CodeIgniter\Validation\FileRules::class,
        \CodeIgniter\Validation\CreditCardRules::class,
        \Navindex\Auth\Validation\Rules::class,
    ];
```

1. Ensure your database is setup correctly, then run the Auth migrations:

```bash
    > php spark migrate -all
```

## 5. Overview

When first installed, **Navindex:Auth** is setup to provide all of the basic authentication services for you, including new user registration, login/logout, and forgotten password flows.

"Remember Me" functionality is turned off by default though it can be turned on by setting the `$allowRemembering` variable to be `true` in **Config/Auth.php**.

### 5.1. Routes

Routes are defined in Auth's **Config/Routes.php** file. This file is automatically located by CodeIgniter when it is processing the routes. If you would like to customize the routes, you should copy the file to the **app/Config** directory and make your changes there.

### 5.2. Views

Basic views are provided for all features.

You can easily override the views used by editing **Config/Auth.php**, and changing the appropriate values within the `$views` variable:

```php
    public $views = [
        'login'         => 'Navindex\Auth\Views\login',
        'register'      => 'Navindex\Auth\Views\register',
        'forgot'        => 'Navindex\Auth\Views\forgot',
        'reset'         => 'Navindex\Auth\Views\reset',
        'emailActivate' => 'Navindex\Auth\Views\emails\activate',
        'emailForgot'   => 'Navindex\Auth\Views\emails\forgot',
    ];
```

NOTE: If you're not familiar with how views can be namespaced in CodeIgniter, please refer to [the user guide](https://codeigniter4.github.io/CodeIgniter4/general/modules.html) for CI4's Code Module support.

## 6. Services

The following Services are provided by the package:

### 6.1. Authentication

Provides access to any of the authentication packages that **Navindex:Auth** knows about. By default it will return the "Local Authentication" library, which is the basic password-based system.

```php
    $authenticate = service('authentication');
```

You can specify the library to use as the first argument:

```php
    $authenticate = service('authentication', 'jwt');
```

### 6.2. Authorisation

Provides access to any of the authorization libraries that **Navindex:Auth** knows about. By default it will return the "Flat" authorization library, which is a Flat RBAC (role-based access control) as defined by NIST. It provides user-specific permissions as well as role based permissions.

```php
    $authorise = service('authorization');
```

### 6.3. Password validation

Provides direct access to the Password validation system. This is an expandable system that currently supports many of [NIST's latest Digital Identity guidelines](https://pages.nist.gov/800-63-3/). The validator comes with a dictionary of over 620,000 common/leaked passwords that can be checked against.
A handful of variations on the user's email/username are automatically checked against.

```php
    $validate = service('passwords');
```

Most of the time you should not need to access this library directly, though, as a new Validation rule is provided that can be used with the Validation library, `strong_password`. In order to enable this, you must first edit **app/Config/Validation.php** and add the new ruleset to the available rule sets:

```php
    public $ruleSets = [
        \CodeIgniter\Validation\Rules::class,
        \CodeIgniter\Validation\FormatRules::class,
        \CodeIgniter\Validation\FileRules::class,
        \CodeIgniter\Validation\CreditCardRules::class,
        \Navindex\Auth\Validation\Rules::class,
    ];
```

Now you can use `strong_password` in any set of rules for validation:

```php
    $validation->setRules([
        'username' => 'required',
        'password' => 'required|strong_password'
    ]);
```

## 7. Helper Functions

**Navindex:Auth** comes with its own [Helper](https://codeigniter4.github.io/CodeIgniter4/general/helpers.html) that includes the following helper functions to ease access to basic features. Be sure to load the helper before using these functions: `helper('auth');`

**Hint**: Add `'auth'` to any controller's `$helper` property to have it loaded automatically, or the same in **app/Controllers/BaseController.php** to have it globally available. the auth filters all pre-load the helper so it is available on any filtered routes.

### 7.1. logged_in()

- Function: Checks to see if any user is logged in.
- Parameters: None
- Returns: `true` or `false`

### 7.2. user()

- Function: Returns the User instance for the current logged in user.
- Parameters: None
- Returns: The current User entity, or `null`

### 7.3. user_id()

- Function: Returns the User ID for the current logged in user.
- Parameters: None
- Returns: The current User's integer ID, or `null`

### 7.4. has_role()

- Function: Ensures that the current user has at least one of the roles passed.
- Parameters: Role IDs or names, as either a single item or an array of items.
- Returns: `true` or `false`

### 7.5. has_permission()

- Function: Ensures that the current user has at least one of the passed in permissions.
- Parameters: Permission ID or name.
- Returns: `true` or `false`

## 8. Models and Entities

### 8.1. User

**Navindex:Auth** uses [CodeIgniter Entities](https://codeigniter4.github.io/CodeIgniter4/models/entities.html) for it's User object. This class provides automatic password hashing as well as utility methods for banning/un-banning, password reset hash generation, and more.
Your application can use a different class, however, the **Entities/UserInterface.php** interface must be implemented. You also need to change *all* class references in **Config/Auth.php**. For example:

```php
    public $authorisators = [
        FlatAuthorisation::class => [
            'models' => [
                'user'       => UserModel::class,
                'token'      => PersistenceTokenModel::class,
                'role'       => RoleModel::class,
                'permission' => PermissionModel::class,
            ],
            'entities' => [
                'user'       => App\Entities\MyUser::class,
                'token'      => PersistenceToken::class,
                'role'       => Type::class,
                'permission' => Type::class,
            ]
        ],
    ];
```

It also provides a UserModel that should be used as it provides methods needed during the password-reset flow, as well as basic validation rules. You are free to extend this class or modify it as needed. You can also create your own user model class by implementing **Models/UserModelInterface.php** interface.

The **UserModel** can automatically assign a role during user creation. Pass the role name to the `setDefaultRole()` method prior to calling `insert()` or `save()` to create a new user and the role will be automatically added to that user.

```php
    $user = $userModel->setDefaultRole('guest')->insert($data);
```

User registration already handles this for you, and looks to the Auth config file's `$defaultRole` setting for the name of the role to add to the user. Please, keep in mind that `$defaultRole` variable is not set by default.

## 9. Toolbar

**Navindex:Auth** includes a toolbar collector to make it easy for developers to work with and troubleshoot the authentication process. To enable the collector, edit **app/Config/Toolbar.php** and add it to the list of active collectors:

```php
    public $collectors = [
        \CodeIgniter\Debug\Toolbar\Collectors\Timers::class,
        \CodeIgniter\Debug\Toolbar\Collectors\Database::class,
        ...
        \Navindex\Auth\Collectors\Auth::class,
    ];
```

## 10. Restricting by Route

If you specify each of your routes within the **app/Config/Routes.php** file, you can restrict access to users by group/role or permission with [Controller Filters](https://codeigniter4.github.io/CodeIgniter4/incoming/filters.html).

First, edit **app/Config/Filters.php** and add the following entries to the `aliases` property:

```php
    'login'      => \Navindex\Auth\Filters\LoginFilter::class,
    'role'       => \Navindex\Auth\Filters\RoleFilter::class,
    'permission' => \Navindex\Auth\Filters\PermissionFilter::class,
```

### 10.1. Global restrictions

The role and permission filters require additional parameters, but `LoginFilter` can be used to restrict portions of a site (or the entire site) to any authenticated user. If no logged in user is detected then the filter will redirect users to the login form.

Restrict routes based on their URI pattern by editing **app/Config/Filters.php** and adding them to the `$filters` array, e.g.:

```php
public filters = [
    'login' => ['before' => ['account/*']],
];
```

Or restrict your entire site by adding the `LoginFilter` to the `$globals` array:

```php
    public $globals = [
        'before' => [
            'honeypot',
            'login',
    ...
```

### 10.2. Restricting a single route

Any single route can be restricted by adding the `filter` option to the last parameter in any of the route definition methods:

```php
    $routes->get('admin/users', 'UserController::index', ['filter' => 'permission:manage-user'])
    $routes->get('admin/users', 'UserController::index', ['filter' => 'role:admin,superadmin'])
```

The filter can be either `role` or `permission`, which restricts the route by either role or permission. You must add a comma-separated list of groups or permissions to check the logged in user against.

### 10.3. Restricting Route Groups

In the same way, entire groups of routes can be restricted within the `group()` method:

```php
    $routes->group('admin', ['filter' => 'role:admin,superadmin'], function($routes) {
    ...
});
```

## 11. Customization

This library is intentionally slim. You will likely want to use your own database structure or a different authorisation method.

### 11.1. Entities

### 11.2. Models

### 11.3. Controller

### 11.4. Authenticator

### 11.5. Activator

### 11.6. Validator

### 11.7. Resetter

### 11.8. Validator

### 11.9. Authorisator

You can create your own migration to add these fields (see: [an example migration](bin/20190603101528_alter_table_users.php).
If you used `auth:publish` you can also add these fields to your `UserModel`'s `$allowedFields` property.

## 12. Credits

Thanks to Lonnie Ezell and all the other developers who are tirelessly working on  [Myth:Auth](https://github.com/lonnieezell/myth-auth).

Thanks to [EllisLab](https://ellislab.com) for originally creating CodeIgniter and the [British Columbia Institute of Technology](https://bcit.ca/) for continuing the project. Thanks to all the developers and contibutors working on [CodeIgniter 4](https://github.com/bcit-ci/CodeIgniter4).
