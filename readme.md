#Empathy

##Description
Empathy is the little php framework that could! (Heavily) Inspired by Rails it's like Laravel but not as well supported, documented, tested or advanced. But keep reading!

It gives you a full ORM, Routing, Migration and Templating solution as well as a CLI for the boring day to day tasks, an interactive console and development server (it's just the standard php one.. don't get excited).

#Installation
```sh
gem build empathy.gemspec
gem install empathy-0.0.1.gem
```

You will be able to just do `gem install empathy` as soon as this is pushed to rubygems

##Creating your first app
```sh
empathy new my_app
cd my_app/
empathy serve
```
That's it, grab a piece of cake, you've just created your first Empathy app. Open up your favorite chrome browser and navigate to localhost:1338 to see it. (You can run `empathy serve 3000` if you want to run the server on port 3000, any other port).


##Routing
Having your app do something when someone goes to a URL is a good start. Let's begin but creating a /home url. Open up your `config/routes.config.php`. 

```php
<?php
  $router = new Router();
  $router->get('home', function(){
     echo "Welcome to my new app!";
  });
  $router->run();
?>
```

Pretty simple. When there is a `GET` request that matches `home/` run the function provided. This is the most basic usage. But we can do some more fun stuff if we add a route that goes to a controller method. Add the following lines before the the router is run;

```php
$router->index = function(){
  echo "Hello, this is my new app";
}
$router->get('user/all', 'user#all');
$router->get('user/:id', 'user#profile');
```

###How do i create a controller?
Fear not, just run `empathy generate controller user all profile`

This will create `controller/UserController.php`, add the necessary functions to it, as well as create the views in `views/user/all.html` `views/user/profile.html`

###Controllers
In your new fancy UserController.php, make the `all` function look like this

```php
function all() {
  User::all()->each(function($user) {
    echo $user->firstname;
  });
}
```

But before this will work, you will need a shiny User model!

###Models
As always you can generate a model by using the CLI 
```sh
empathy g m user "email:varchar(255)" "password:varchar(60)" "firstname:varhar(25)" "lastname:varchar(25)"
```

The `g m` is shorthand for `generate model`, you can do `empathy g c` as well for controllers or `empathy d c` to delete a controller (or model)

This will create a file in `models/User.php` as well as a migration file in `migrations/%timestamp%_create_user.sql`. Run the migration with `empathy migrate`. This will bring the database schema up to date.

###Using the interactive console
Just as a kickstart, let's create a user using the interactive console. Run `empathy console` (shorthand `empathy c`).
In the console, run the following snippet.

```php
$user = new User([
  'email' => "tester@mctest.com",
  'password' => password_hash("ilikecake", PASSWORD_DEFAULT),
  'firstname' => 'Test',
  'lastname' => 'McTest'
]);
$user->save();
```

You can retrieve it again with

```php
echo json_encode(User::where(['email' => 'tester@mctest.com'])->first());
```

That's it! You've done it! If you refresh the browser now, you should see Test McTesty listed in the user list.
