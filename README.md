## Laravel Package Manager ##
---

The `Laravel Package Manager` provides fast, yet simple management of packages for your Laravel project.
It allows you to quickly install a package via composer, if necessary, and then automatically register any or all Service Providers and Facades provided by the package.

---
### Installation
---

To install, first install with composer: 

	composer require patinthehat/laravel-package-manager
	
Then, register the service provider by editing `config/app.php` and adding:

	LaravelPackageManager\LaravelPackageManagerServiceProvider::class,
	
to the `$providers` array.
	
That's it! You now have access to the package manager commands through `artisan`.

---
### Usage
---

To install (via composer) a package and register any service providers or Facades it provides, use the `package:require` command:

	package:require <package-name> [-r|--register-only] [-d|--dev]
	
The `--register-only` option skips the composer installation step.
		The `--dev` option allows you to install the package in your development dependencies.

To unregister service providers and facades associated with a package, use the `package:unregister` command:

	package:unregister <package-name>

You will be prompted for each Service Provider and Facade, and asked if you would like to unregister it.  This does not remove the package from your `vendor/` directory.

---
### License
---
The `Laravel Package Manager` is open source software, available under the [MIT License](LICENSE).