#Filter-Nator

This bundle is a simple link between the [KnpPaginatorBundle][1] and
the [LexikFormFilterBundle][2] allowing entities to be filtered (Filter) and
paginated (Nator).

##Installation

###Step 1

Add the Filter-Nator bundle as a dependency in your composer.json:

```json
require: {
    "savvy/filternator-bundle": "dev-master"
}
```

###Step 2
Update the dependecies using composer:

```shell
$ php composer.phar update
```

###Step 3
Add the Filter-Nator bundle to the AppKernal.php file:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            //...
            new Savvy\FilterNatorBundle\SavvyFilterNatorBundle(),
```

##Usage

The Filter-Nator bundle has one method, `filterNate()`.  This method
requires a query builder object, a filter form and a unique string
to be used to store the form data in the session.  The returned value
is the pagination object from the [KnpPaginatorBundle][1]:

```php
//Any class with access to the ContainerInterface object
$pagination = $this->container->get("savvy.filter_nator")->filterNate($filterBuilder, $form, 'foo');
```

###Options

There are two additional arguments that can be given to `filterNate()` to set the required number
of entites to return and the page number to start on:

```php
//Any class with access to the ContainerInterface object
$pagination = $this->container->get("savvy.filter_nator")->filterNate(
    $filterBuilder,
    $form,
    'foo',
    5, /*return 5 entities*/
    1  /*starting from page 1*/
);
```

[1]: https://github.com/KnpLabs/KnpPaginatorBundle
[2]: https://github.com/lexik/LexikFormFilterBundle