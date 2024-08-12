# Upgrade Guide

With every upgrade, make sure to re-publish StreamTalk's assets:

## For v0.0.4 and earlier versions

```
php artisan verndor:publish --tag=streamtalk-views --force
php artisan verndor:publish --tag=streamtalk-assets --force
```

If needed, you can re-publish the other assets the same way above by just replacing the name of the asset (StreamTalk-NAME).

## For v0.0.3+ and higher versions

To re-publish only `views` & `assets`:

```
php artisan streamtalk:publish
```

To re-publish all the assets (views, assets, config..):

```
php artisan streamtalk:publish --force
```

> This will overwrite all the assets, so all your changes will be overwritten.
